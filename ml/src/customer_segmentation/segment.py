from sklearn.cluster import KMeans
import pandas as pd
from db.mysql_connector import MySQLConnector

class CustomerSegmentation:
    def __init__(self, n_clusters=3):
        self.n_clusters = n_clusters
        self.model = KMeans(n_clusters=self.n_clusters)

    def load_data(self, file_path):
        data = pd.read_csv(file_path)
        return data

    def preprocess_data(self, data):
        # Assuming 'product_type' is the feature for segmentation
        data_encoded = pd.get_dummies(data['product_type'])
        return data_encoded

    def fit(self, data):
        self.model.fit(data)

    def predict(self, data):
        return self.model.predict(data)

    def save_segments_to_db(self, segments, customer_ids):
        connector = MySQLConnector()
        for customer_id, segment in zip(customer_ids, segments):
            connector.execute_query("INSERT INTO customer_segments (customer_id, segment) VALUES (%s, %s)", (customer_id, segment))

    def segment_customers(self, file_path):
        data = self.load_data(file_path)
        preprocessed_data = self.preprocess_data(data)
        self.fit(preprocessed_data)
        segments = self.predict(preprocessed_data)
        self.save_segments_to_db(segments, data['customer_id'])