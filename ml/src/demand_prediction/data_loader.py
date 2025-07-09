import pandas as pd
from sklearn.preprocessing import MinMaxScaler
import numpy as np


class DemandDataLoader:
    def __init__(self, csv_path):
        # Load and rename for consistency
        df = pd.read_csv(csv_path, parse_dates=['purchase_date'])
        df = df.rename(columns={
            'purchase_date': 'date',
            'product': 'product_id',
            'quantity': 'quantity_sold'
        })

        # Aggregate by date and product
        self.data = (
            df
            .groupby(['date', 'product_id'], as_index=False)
            .agg({'quantity_sold': 'sum'})
            .sort_values(['product_id', 'date'])
        )

        self.scaler = MinMaxScaler()
        print("📂 Columns in CSV:", self.data.columns.tolist())
        print("🔍 First 3 rows of data:")
        print(self.data.head(3))

    def prepare_series(self, product_id, look_back=30):
        df_product = self.data[self.data['product_id'] == product_id]
        if df_product.empty:
            raise ValueError(f"No data found for product {product_id}")

        sub = self.data[self.data['product_id'] == product_id].copy()
        values = sub['quantity_sold'].values.reshape(-1, 1)
        scaled = self.scaler.fit_transform(values)

        X, y = [], []
        for i in range(len(scaled) - look_back):
            X.append(scaled[i:i+look_back])
            y.append(scaled[i+look_back, 0])

        return np.array(X), np.array(y), sub.reset_index(drop=True), self.scaler
