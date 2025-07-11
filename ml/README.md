# Supply Chain Machine Learning Module

This project implements machine learning features to optimize the supply chain through data-driven insights. It includes two main functionalities: Demand Prediction and Customer Segmentation.

## Features

1. **Demand Prediction**
   - Utilizes an LSTM model built with TensorFlow to predict demand for the next quarter.
   - Accounts for seasonal peaks (e.g., Christmas).
   - Predictions are saved to a MySQL database and visualized on a Laravel dashboard using Chart.js.

2. **Customer Segmentation**
   - Implements K-means clustering using scikit-learn to segment customers based on their purchasing behavior.
   - Segments customers into categories (e.g., dark chocolate lovers vs. milk chocolate buyers).
   - Segments are stored in MySQL, enabling product recommendations based on customer segments.

## Project Structure

```
supply-chain-ml-module
├── src
│   ├── demand_prediction
│   │   ├── lstm_model.py
│   │   ├── data_loader.py
│   │   └── predict.py
│   ├── customer_segmentation
│   │   ├── kmeans_model.py
│   │   ├── data_loader.py
│   │   └── segment.py
│   ├── db
│   │   └── mysql_connector.py
│   └── utils
│       └── helpers.py
├── requirements.txt
├── README.md
└── config.yaml
```

## Setup Instructions

1. Clone the repository:
   ```
   git clone <repository-url>
   cd ml
   ```

2. Install the required dependencies:
   ```
   pip install -r requirements.txt
   ```

3. Configure the database connection in `config.yaml`.

4. Run the demand prediction and customer segmentation scripts as needed.

## Usage

- To train the LSTM model for demand prediction, execute the script in `src/demand_prediction/predict.py`.
- To perform customer segmentation, run the script in `src/customer_segmentation/segment.py`.

## License

This project is licensed under the MIT License.