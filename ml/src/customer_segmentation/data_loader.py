import pandas as pd
import numpy as np
from datetime import datetime

class CustomerDataLoader:
    def __init__(self, filepath):
        self.filepath = filepath
        self.data = None
        self.features = None

    def load_data(self):
        """Load and preprocess the data with robust fallbacks"""
        self.data = pd.read_csv(self.filepath)
        
        # Verify minimum required columns
        mandatory_cols = ['Customer_ID', 'quantity']
        missing = [col for col in mandatory_cols if col not in self.data.columns]
        if missing:
            raise ValueError(f"Missing mandatory columns: {missing}")

        # Create basic features
        self.features = self.data.groupby('Customer_ID').agg({
            'quantity': ['sum', 'count']  # total_quantity and purchase_count
        })
        
        # Flatten multi-index columns
        self.features.columns = ['total_quantity', 'purchase_count']
        
        # Add raw quantity sample
        self.features['quantity'] = self.data.groupby('Customer_ID')['quantity'].first()
        
        return self.features.reset_index()

    def get_features(self):
        """Get features with guaranteed columns"""
        if self.data is None:
            self.load_data()
            
        return self.features[['quantity', 'total_quantity', 'purchase_count']].copy()

    def merge_clusters(self, cluster_labels):
        """Merge cluster labels back with full features and original data"""
        if len(cluster_labels) != len(self.features):
            raise ValueError("Cluster labels length mismatch")

    # Reset index to get Customer_ID into a column
        clustered = self.features.reset_index()
        clustered['cluster'] = cluster_labels

    # Merge all feature columns + cluster back into the full dataset
        return pd.merge(
        self.data,
        clustered,  # include full features + cluster
        on='Customer_ID',
        how='left'
    )
