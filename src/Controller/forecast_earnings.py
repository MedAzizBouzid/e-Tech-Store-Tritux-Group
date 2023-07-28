import pandas as pd
import sys
import json
from pmdarima.arima import AutoARIMA
from datetime import datetime, timedelta


def forecast_earnings(earnings_data):
    try:
        # Transform the earnings_data into a list of dictionaries
        data_list = [{'date': row[0], 'earnings': row[1]}
                     for row in earnings_data]

        # Convert the transformed data into a pandas DataFrame
        df = pd.DataFrame(data_list)

        # Set the 'date' column as the index and convert it to datetime
        df['date'] = pd.to_datetime(df['date'])
        df.set_index('date', inplace=True)

        # Resample the data to daily frequency and fill missing values with 0
        df = df.resample('D').sum().fillna(0)
        seasonal = False
        # Use AutoARIMA to find the best ARIMA model
        model = AutoARIMA(start_p=1, d=None, start_q=1, max_p=3,
                          max_d=2, max_q=3, seasonal=seasonal, stepwise=True)
        results = model.fit(df)

        # Get the number of days to forecast (e.g., 10 days)
        num_days_to_forecast = 10

        # Get the last date in the historical data and start forecasting from the next day
        last_date = df.index[-1]
        start_date = last_date + timedelta(days=1)

        # Generate the forecasted dates
        forecast_dates = pd.date_range(
            start=start_date, periods=num_days_to_forecast)

        # Forecast earnings for the next 10 days
        forecast = results.predict(n_periods=num_days_to_forecast)

        # Create a list of dictionaries with 'date' and 'earnings' keys
        forecast_data = [{'date': date.strftime(
            '%Y-%m-%d'), 'earnings': earnings} for date, earnings in zip(forecast_dates, forecast)]

        return forecast_data

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
    sys.stdout.write(json.dumps(forecasted_earnings))
