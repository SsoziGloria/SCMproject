from sklearn.cluster import KMeans
import pandas as pd

class CustomerSegmentation:
    def __init__(self, n_clusters=5):
        self.n_clusters = n_clusters
        self.model = KMeans(n_clusters=self.n_clusters)

    def fit(self, data):
        self.model.fit(data)

    def predict(self, data):
        return self.model.predict(data)

    def get_cluster_centers(self):
        return self.model.cluster_centers_

    def get_labels(self):
        return self.model.labels_

def load_customer_data(file_path):
    data = pd.read_csv(file_path)
    return data[['customer_id', 'product_type']]  # Adjust based on actual dataset structure

def main():
    # Example usage
    data = load_customer_data('path_to_customer_data.csv')
    segmentation = CustomerSegmentation(n_clusters=3)
    segmentation.fit(data)
    labels = segmentation.predict(data)
    print(labels)

if __name__ == "__main__":
    main()