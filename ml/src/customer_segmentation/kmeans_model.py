import pickle
import numpy as np
import pandas as pd
from sklearn.cluster import KMeans
from sklearn.preprocessing import RobustScaler
from sklearn.decomposition import PCA
from sklearn.metrics import silhouette_score, davies_bouldin_score

class CustomerSegmentation:
    def __init__(self, n_clusters=4, use_pca=False, random_state=42):
        self.n_clusters = n_clusters
        self.use_pca = use_pca
        self.random_state = random_state
        self.model = None
        self.scaler = RobustScaler()
        self.pca = PCA(n_components=2) if use_pca else None
        self.fitted = False

    def fit(self, data):
        """Fit the model to the data."""
        self.scaled_data = self.scaler.fit_transform(data)
        
        if self.use_pca:
            self.scaled_data = self.pca.fit_transform(self.scaled_data)
            
        self.model = KMeans(
            n_clusters=self.n_clusters,
            random_state=self.random_state
        )
        self.model.fit(self.scaled_data)
        self.fitted = True
        
        # Calculate metrics
        labels = self.model.labels_
        print(f"\nClustering Metrics:")
        print(f"Silhouette Score: {silhouette_score(self.scaled_data, labels):.3f}")
        print(f"Davies-Bouldin Index: {davies_bouldin_score(self.scaled_data, labels):.3f}")

    def predict(self, data):
        """Predict clusters for new data."""
        if not self.fitted:
            raise RuntimeError("Model not fitted yet.")
        scaled = self.scaler.transform(data)
        if self.use_pca:
            scaled = self.pca.transform(scaled)
        return self.model.predict(scaled)

    def get_cluster_centers(self):
        """Get the cluster centers."""
        if not self.fitted:
            raise RuntimeError("Model not fitted yet.")
        return self.model.cluster_centers_

    def get_labels(self):
        """Get cluster labels for training data."""
        if not self.fitted:
            raise RuntimeError("Model not fitted yet.")
        return self.model.labels_

    def get_visualization_data(self, original_features):
        """Prepare data for frontend visualization."""
        if not self.fitted:
            raise RuntimeError("Model not fitted yet.")
            
        return {
            'cluster_centers': self.get_cluster_centers().tolist(),
            'features': original_features.columns.tolist(),
            'metrics': {
                'silhouette': silhouette_score(self.scaled_data, self.get_labels()),
                'davies_bouldin': davies_bouldin_score(self.scaled_data, self.get_labels())
            }
        }