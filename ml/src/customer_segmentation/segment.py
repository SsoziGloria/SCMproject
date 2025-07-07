from customer_segmentation.kmeans_model import CustomerSegmentation
from customer_segmentation.data_loader import (
    load_customer_data,
    preprocess_data,
    extract_features
)
from db.mysql_connector import MySQLConnector  # Import the connector
import os
import pandas as pd
import sqlalchemy
import seaborn as sns
import matplotlib.pyplot as plt

plt.rcParams['font.family'] = 'Segoe UI Emoji'
plt.rcParams['font.sans-serif'] = ['Segoe UI Emoji']


def save_segments_to_db(features_df):
    try:
        #Connect to MySQL
        connector = MySQLConnector(
            user='root',
            password='',
            host='127.0.0.1',
            database='chocolate_scm'
        )
        connector.connect()

        for _, row in features_df.iterrows():
            customer_id = row['customer_id']
            total_items_bought = int(row['total_items_bought'])
            recency_days = int(row['recency_days'])
            num_orders = int(row['num_orders'])
            segment = int(row['segment'])

            with connector.engine.connect() as connection:
                connection.execute(
                        sqlalchemy.text("""
                        INSERT INTO customer_segments 
                (customer_id, total_items_bought, recency_days, num_orders, segment)
            VALUES (:customer_id, :total_items_bought, :recency_days, :num_orders, :segment)
            ON DUPLICATE KEY UPDATE 
                total_items_bought = VALUES(total_items_bought),
                recency_days = VALUES(recency_days),
                segment = VALUES(segment)
        """),
        {
            'customer_id': customer_id,
            'total_items_bought': total_items_bought,
            'recency_days': recency_days,
            'num_orders': num_orders,
            'segment': segment
        }
    )


        print("✅ Segments saved to MySQL database.")

    except Exception as e:
        print(f"❌ Error saving to DB: {e}")
    finally:
        if connector.engine:
            connector.engine.dispose()


def segment_customers(file_path, n_clusters=3, output_file='customer_segments.csv'):
    # Checks if file exists
    if not os.path.exists(file_path):
        print(f"❌ Error: File '{file_path}' not found.")
        return

    # Load and process the data
    raw_data = load_customer_data(file_path)
    clean_data = preprocess_data(raw_data)
    features_df = extract_features(clean_data)

    # Select features
    feature_columns = ['total_items_bought', 'num_orders', 'recency_days']
    X = features_df[feature_columns]

    # Train KMeans model
    model = CustomerSegmentation(n_clusters=n_clusters)
    model.fit(X)

    # Predict segments
    features_df['segment'] = model.get_labels()

    print("\n🧠 Segmented Customers:\n")
    print(features_df.to_string(index=False))

    print("\n📊 Segment Distribution:")
    print(features_df['segment'].value_counts())

    # Save to CSV
    features_df.to_csv(output_file, index=False)
    print(f"\n💾 Saved to '{output_file}'.")

    # Save to MySQL
    save_segments_to_db(features_df)

    # Visualization (currently still scatter)
    sns.set(style="whitegrid")
    sns.scatterplot(
        x='recency_days',
        y='total_items_bought',
        hue='segment',
        palette='Set2',
        data=features_df
    )

    os.makedirs("public/plots", exist_ok=True)  #it Auto-creates folder if it doesn't exist
    plt.title('Customer Segments')
    plt.xlabel('Recency (Days)')
    plt.ylabel('Total Items Bought')
    plt.tight_layout()
    plt.savefig("public/plots/segments.png")  # can be used for dashboard
    plt.show()


if __name__ == "__main__":
    segment_customers("Cleaned_Chocolate_Sales.csv")
