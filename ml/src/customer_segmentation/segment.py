import json

import matplotlib.pyplot as plt
import numpy as np
import pandas as pd
from customer_segmentation.data_loader import CustomerDataLoader
from customer_segmentation.kmeans_model import CustomerSegmentation
from db.mysql_connector import MySQLConnector
from sklearn.exceptions import NotFittedError
from sklearn.metrics import davies_bouldin_score, silhouette_score
from sqlalchemy import create_engine, text


def validate_features(features):
    """Simplified validation with debug output"""
    required = ["quantity", "total_quantity", "purchase_count"]

    print("\n=== VALIDATION DEBUG ===")
    print("Input columns:", features.columns.tolist())
    print("Data types:\n", features.dtypes)

    missing = [col for col in required if col not in features.columns]
    if missing:
        raise ValueError(
            f"Missing required columns: {missing}\n"
            f"Available columns: {features.columns.tolist()}\n"
            f"First row:\n{features.iloc[0] if len(features) > 0 else 'Empty'}"
        )

    return features[required].copy()


def analyze_clusters(clustered_data, features_used):
    """Generate cluster statistics."""
    stats = clustered_data.groupby("cluster")[features_used].agg(
        ["mean", "median", "std", "count"]
    )
    stats.columns = ["_".join(col).strip() for col in stats.columns.values]

    # Add percentiles
    for p in [10, 25, 75, 90]:
        stats[f"quantity_q{p}"] = clustered_data.groupby("cluster")[
            "quantity"
        ].quantile(p / 100)

    stats["customer_count"] = clustered_data.groupby("cluster")["Customer_ID"].nunique()
    return stats.sort_values("quantity_mean", ascending=False)


def visualize_clusters(model, features):
    """Generate PCA visualization if available."""
    if not model.use_pca:
        return

    plt.figure(figsize=(10, 6))
    scatter = plt.scatter(
        model.scaled_data[:, 0],
        model.scaled_data[:, 1],
        c=model.get_labels(),
        cmap="viridis",
        alpha=0.6,
        s=50,
    )
    plt.scatter(
        model.model.cluster_centers_[:, 0],
        model.model.cluster_centers_[:, 1],
        marker="X",
        s=200,
        c="red",
        label="Centroids",
    )
    plt.title("Customer Segments (PCA)")
    plt.xlabel("Principal Component 1")
    plt.ylabel("Principal Component 2")
    plt.colorbar(scatter, label="Cluster")
    plt.grid(alpha=0.3)
    plt.tight_layout()
    plt.show()


def create_table_if_not_exists(engine):
    """Create customer_segments table if not exists."""
    create_table_query = """
    CREATE TABLE IF NOT EXISTS customer_segments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        quantity FLOAT,
        total_quantity FLOAT,
        purchase_count INT,
        cluster INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    """
    with engine.connect() as conn:
        conn.execute(text(create_table_query))
    print("✅ Ensured customer_segments table exists")


def save_segments(engine, df):
    """Save customer segments DataFrame rows into the database."""
    insert_query = """
    INSERT INTO customer_segments (customer_id, quantity, total_quantity, purchase_count, cluster)
    VALUES (:customer_id, :quantity, :total_quantity, :purchase_count, :cluster)
    """
    with engine.connect() as conn:
        for _, row in df.iterrows():
            conn.execute(
                text(insert_query),
                {
                    "customer_id": int(row["Customer_ID"]),
                    "quantity": float(row["quantity"]),
                    "total_quantity": float(row["total_quantity"]),
                    "purchase_count": int(row["purchase_count"]),
                    "cluster": int(row["cluster"]),
                },
            )
        conn.commit()
    print(f"✅ Inserted {len(df)} records into customer_segments")


def main():
    try:
        # Configuration
        DATA_PATH = "Cleaned_Chocolate_Sales.csv"
        OUTPUT_PATH = "clustered_customers.csv"
        VISUALIZATION_PATH = "visualization_data.json"
        N_CLUSTERS = 4
        USE_PCA = True

        # 1. Load and validate data
        print("🔄 Loading and validating data...")
        loader = CustomerDataLoader(DATA_PATH)
        features = loader.get_features()

        raw_features = features
        # 2. Validate features
        print("\n=== FINAL FEATURES ===")
        print("Columns:", raw_features.columns.tolist())
        print("Sample:\n", raw_features.head())
        features = validate_features(raw_features.reset_index())

        # 3. Train model
        print("\n🔧 Training model...")
        segmentation = CustomerSegmentation(
            n_clusters=N_CLUSTERS, use_pca=USE_PCA, random_state=42
        )
        segmentation.fit(features[["quantity", "total_quantity", "purchase_count"]])

        # 4. Save results
        raw_features = raw_features.reset_index()  # Make Customer_ID a column
        raw_features["cluster"] = segmentation.get_labels()
        clustered_data = raw_features
        clustered_data.to_csv(OUTPUT_PATH, index=False)
        print(f"\n💾 Results saved to {OUTPUT_PATH}")

        # ✅ Save to MySQL using MySQLConnector
        mysql = MySQLConnector(
            user="root", password="00000000", host="127.0.0.1", database="chocolate_scm"
        )
        engine = mysql.get_engine()
        create_table_if_not_exists(engine)
        save_segments(engine, clustered_data)

        # ===== 5. Generate Visualization Data =====
        print("\n📊 Preparing visualization data...")
        stats = analyze_clusters(
            clustered_data, ["quantity", "total_quantity", "purchase_count"]
        )
        print(stats)
        # 6. Generate visualization data

        viz_data = {
            "cluster_centers": segmentation.get_cluster_centers().tolist(),
            "features": features.columns.tolist(),
            "metrics": {
                "silhouette": silhouette_score(
                    segmentation.scaled_data, segmentation.get_labels()
                ),
                "davies_bouldin": davies_bouldin_score(
                    segmentation.scaled_data, segmentation.get_labels()
                ),
            },
            "cluster_stats": stats.to_dict(),
        }

        with open(VISUALIZATION_PATH, "w") as f:
            json.dump(viz_data, f, indent=2)
        print(f"📈 Visualisation data saved to '{VISUALIZATION_PATH}'")

        # 7. Visualize IF PCA is used
        if USE_PCA:
            visualize_clusters(segmentation, features)

    except Exception as e:
        print(f"❌ Error: {str(e)}")
    finally:
        print("\n🏁 Process completed")


if __name__ == "__main__":
    main()
