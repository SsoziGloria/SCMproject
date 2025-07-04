import pandas as pd


def load_customer_data(file_path='Cleaned_Chocolate_Sales.csv'):
    """
    Load customer transaction data from CSV and convert date columns.
    """
    data = pd.read_csv('Cleaned_Chocolate_Sales.csv')

    # Convert purchase_date to datetime format
    data['purchase_date'] = pd.to_datetime(data['purchase_date'])

    return data


def preprocess_data(data):
    """
    Clean data by removing missing values.
    """
    # Drop rows with any missing values
    data = data.dropna()

    # Rename columns to keep consistent naming
    data.rename(columns={
        'Customer_ID': 'customer_id',
        'product': 'product_name'
    }, inplace=True)

    return data


def extract_features(data):
    """
    Extract per-customer features: total quantity bought, number of orders, recency.
    """
    # Get the latest date in the dataset to calculate recency/ recent purchases
    latest_date = data['purchase_date'].max()

    # Group data per customer to generate features
    customer_data = data.groupby('customer_id').agg({
        'quantity': 'sum',           # total quantity bought
        'product_name': 'count',     # number of orders (frequency)
        'purchase_date': 'max'       # most recent purchase
    }).reset_index()

    # Rename columns for clarity
    customer_data.rename(columns={
        'quantity': 'total_items_bought',
        'product_name': 'num_orders',
        'purchase_date': 'last_purchase_date'
    }, inplace=True)

    # Calculate 'recency' in days
    customer_data['recency_days'] = (
        latest_date - customer_data['last_purchase_date']).dt.days

    # Drop last_purchase_date ( not used in clustering)
    customer_data.drop(columns=['last_purchase_date'], inplace=True)

    return customer_data
