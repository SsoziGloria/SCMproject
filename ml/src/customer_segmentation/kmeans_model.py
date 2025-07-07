import pandas as pd
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler

# defines a reusable class called CustomerSegmentation
class CustomerSegmentation: 
    def __init__(self, n_clusters=3):# by default 3customer groups are stored(0-high spenders, 1-medium spenders, 2-budget-conscious)
        self.n_clusters = n_clusters
        self.model = KMeans(n_clusters=self.n_clusters, random_state=42)# creates the kmeans model.
        self.scaler = StandardScaler()# a scaler which scales my features
        self.fitted = False # tracks whether the model has been fitted yet

    def fit(self, data):
        """
        Fit the K-Means model on scaled data.
        """
        self.scaled_data = self.scaler.fit_transform(data) # scales the data so that features are on same scale
        self.model.fit(self.scaled_data) # fits the kmeans model to the scaled data
        self.fitted = True # marks the model as fitted

    def predict(self, data):
        """
        Predict cluster labels for new data.
        """
        # checks whether the model is already trained, if yes, scales new data and uses the trained model to predict which cluster each row belongs to
        if not self.fitted:
            raise ValueError("Model not fitted yet.")
        scaled = self.scaler.transform(data)
        return self.model.predict(scaled)

    def get_cluster_centers(self):
        """
        Get the cluster centroids (in scaled feature space).
        """
        if not self.fitted:
            raise ValueError("Model not fitted yet.")
        return self.model.cluster_centers_

    def get_labels(self):
        """
        Get the cluster label assigned to each training example.
        """
        if not self.fitted:
            raise ValueError("Model not fitted yet.")
        return self.model.labels_
