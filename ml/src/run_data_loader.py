from customer_segmentation.data_loader import load_customer_data, preprocess_data, extract_features

# Load, preprocess and extract the data
data = load_customer_data('Cleaned_Chocolate_Sales.csv')
data = preprocess_data(data)
features = extract_features(data)

# Show the resulting customer features
print(features.head())
