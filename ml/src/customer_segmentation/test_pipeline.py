import pandas as pd
from .data_loader import CustomerDataLoader

def test_pipeline():
    loader = CustomerDataLoader("Cleaned_Chocolate_Sales.csv")
    features = loader.get_features()
    
    print("\n=== TEST OUTPUT ===")
    print("Final columns:", features.columns.tolist())
    print("Data types:", features.dtypes)
    print("Sample data:\n", features.head())
    
    assert all(col in features.columns for col in ['quantity', 'total_quantity', 'purchase_count'])

if __name__ == "__main__":
    test_pipeline()