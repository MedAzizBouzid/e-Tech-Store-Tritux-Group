# forecast_earnings.py
import sys
import pandas as pd
import numpy as np
import statsmodels.api as sm
from statsmodels.tsa.arima.model import ARIMA
import json


def forecast_earnings(earnings_data):
    try:
        # Convert the earnings_data to a pandas DataFrame
        df = pd.DataFrame(earnings_data, columns=['date', 'earnings'])

        # Set the date as the index and convert it to datetime
        df['date'] = pd.to_datetime(df['date'])
        df.set_index('date', inplace=True)

        # Resample the data to daily frequency and fill missing values with 0
        df = df.resample('D').sum().fillna(0)

        # Fit the ARIMA model
        # (p, d, q) parameters for ARIMA model
        model = ARIMA(df, order=(2, 1, 2))
        results = model.fit()

        # Forecast earnings for the next 7 days (adjust the steps as needed)
        forecast = results.forecast(steps=7)

        return forecast.tolist()

    except Exception as e:
        # If any error occurs during the forecasting process, print the error message
        print(f"Error occurred: {e}")
        sys.exit(1)


# Check if the script is being called from the command-line
if __name__ == "__main__":
    # Get the historical earnings data from command-line arguments
    try:
        historical_earnings_data = json.loads(sys.argv[1])
    except IndexError:
        print("No historical earnings data provided.")
        sys.exit(1)

    # Call the forecast_earnings function with the historical data
    forecasted_earnings = forecast_earnings(historical_earnings_data)

    # Print the forecasted earnings as JSON
    print(json.dumps(forecasted_earnings))
