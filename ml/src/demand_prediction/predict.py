import os
import sys
from datetime import timedelta

import numpy as np
import pandas as pd
from db.mysql_connector import MySQLConnector
from sklearn.metrics import mean_absolute_error, mean_squared_error
from sqlalchemy import create_engine

from .data_loader import DemandDataLoader
from .lstm_model import create_lstm_model

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), "src")))


def save_to_mysql(df, table_name="demand_predictions"):
    db = MySQLConnector(
        user="root", password="00000000", host="127.0.0.1", database="chocolate_scm"
    )
    db.connect()

    df.to_sql(table_name, con=db.engine, if_exists="replace", index=False)
    print(f"✅ Saved to MySQL table `{table_name}`")

    db.dispose()


def run_forecast_all_products(csv_path, look_back=30, forecast_days=7, epochs=30):
    # Load entire dataset once
    full_df = pd.read_csv(csv_path)
    products = full_df["product"].unique()  # Get list of unique products

    all_forecasts = []  #  collect results for all products

    for product in products:
        print(f"\nForecasting for product: {product}")

        # Prepare data & forecasting for this product
        loader = DemandDataLoader(csv_path)
        X, y, df_daily, scaler = loader.prepare_series(product, look_back)
        # skip if we don't have enough data
        if len(X) == 0:
            print(
                f"⚠️ Not enough data to train model for product: {product}. Skipping..."
            )
            continue

        split = int(len(X) * 0.8)
        X_train, y_train = X[:split], y[:split]
        X_test, y_test = X[split:], y[split:]

        # check train data not empty after split
        if len(X_train) == 0 or len(y_train) == 0:
            print(f"⚠️ Skipping product '{product}' because training split is empty.")
            continue

        model = create_lstm_model((look_back, 1))
        model.compile(optimizer="adam", loss="mse", metrics=["mae"])
        model.fit(
            X_train, y_train, validation_data=(X_test, y_test), epochs=epochs, verbose=0
        )

        # Forecast future demand
        last_seq = X[-1]
        forecast_scaled = []
        for _ in range(forecast_days):
            pred = model.predict(last_seq.reshape(1, look_back, 1), verbose=0)
            forecast_scaled.append(pred[0][0])
            last_seq = np.vstack((last_seq[1:], [[pred[0][0]]]))

        forecast = scaler.inverse_transform(
            np.array(forecast_scaled).reshape(-1, 1)
        ).flatten()
        last_date = df_daily["date"].max()
        dates = [last_date + timedelta(days=i + 1) for i in range(forecast_days)]

        forecast_df = pd.DataFrame(
            {
                "product_id": product,
                "prediction_date": dates,
                "predicted_quantity": forecast.astype(int),
            }
        )

        # ➕ Accuracy evaluation
        y_pred_scaled = model.predict(X_test)
        y_pred = scaler.inverse_transform(y_pred_scaled).flatten()
        y_true = scaler.inverse_transform(y_test.reshape(-1, 1)).flatten()

        rmse = np.sqrt(mean_squared_error(y_true, y_pred))
        mae = mean_absolute_error(y_true, y_pred)

        print(f"✅ Accuracy for '{product}' — RMSE: {rmse:.2f}, MAE: {mae:.2f}")

        all_forecasts.append(forecast_df)

    # Combine all products forecast data into one DataFrame
    combined_forecasts = pd.concat(all_forecasts, ignore_index=True)

    print("\n📈 Combined forecast for all products:")
    print(combined_forecasts)

    # Save all forecasts to MySQL once
    save_to_mysql(combined_forecasts)

    return combined_forecasts


if __name__ == "__main__":
    run_forecast_all_products("Cleaned_Chocolate_Sales.csv")
