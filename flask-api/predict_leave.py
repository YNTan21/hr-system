import joblib
import pandas as pd
import requests
import json
from datetime import datetime, timedelta
import matplotlib.pyplot as plt
import os

# Load the trained model
model = joblib.load("leave_prediction_xgb_model.joblib")

# Fetch latest leave data from Laravel API
api_url = "http://hr-system.test/api/get-leave-data"  # Update with actual API URL

try:
    response = requests.get(api_url, timeout=10)
    response.raise_for_status()  # Raise error for bad responses (4xx, 5xx)
    leave_data = response.json()

    # Ensure we have data
    if not leave_data:
        print("No leave data received. Exiting.")
        exit()

except requests.exceptions.RequestException as e:
    print(f"Error fetching leave data: {e}")
    exit()

# Convert to DataFrame
df = pd.DataFrame(leave_data)

# Convert date column to datetime
df["from_date"] = pd.to_datetime(df["from_date"])

# Aggregate leave count per day
leave_counts = df.groupby("from_date").size().reset_index(name="Leave Count")

# Ensure we have enough data for rolling averages
if len(leave_counts) < 7:
    print("Not enough historical data for prediction.")
    exit()

# Feature Engineering
leave_counts["Year"] = leave_counts["from_date"].dt.year
leave_counts["Month"] = leave_counts["from_date"].dt.month
leave_counts["Day"] = leave_counts["from_date"].dt.day
leave_counts["Weekday"] = leave_counts["from_date"].dt.weekday

# Add Lag Features
leave_counts["Prev_Day_Leaves"] = leave_counts["Leave Count"].shift(1).fillna(0)
leave_counts["Rolling_7Day_Avg"] = leave_counts["Leave Count"].rolling(7).mean().fillna(0)

# Prepare Future Data (Next 30 Days)
future_dates = [datetime.today() + timedelta(days=i) for i in range(30)]
future_df = pd.DataFrame({
    "Year": [d.year for d in future_dates],
    "Month": [d.month for d in future_dates],
    "Day": [d.day for d in future_dates],
    "Weekday": [d.weekday() for d in future_dates],
    "Prev_Day_Leaves": [leave_counts["Leave Count"].iloc[-1]] * 30,  # Use last known value
    "Rolling_7Day_Avg": [leave_counts["Leave Count"].rolling(7).mean().iloc[-1]] * 30
})

# Predict
predictions = model.predict(future_df)

# Plot Predictions
plt.figure(figsize=(10, 5))
plt.plot(future_dates, predictions, marker='o', linestyle='-', color='b', label="Predicted Leaves")
plt.xlabel("Date")
plt.ylabel("Predicted Leave Applications")
plt.title("Leave Application Predictions for Next 30 Days")
plt.xticks(rotation=45)
plt.legend()
plt.grid(True)

# Create directory if it doesn't exist
save_dir = 'C:/Users/Acer/Projects/hr-system/public/images'
os.makedirs(save_dir, exist_ok=True)

# Save the plot with full path
save_path = os.path.join(save_dir, 'leave_trend.png')
plt.savefig(save_path, bbox_inches='tight', dpi=300)
plt.close()

# Print success message for debugging
print(f"Graph saved to: {save_path}")

# Continue with JSON output for predictions
result = [{"date": d.strftime('%Y-%m-%d'), "predicted_leaves": int(round(p))} for d, p in zip(future_dates, predictions)]
print(json.dumps(result))

# Save Predictions Back to Laravel Database
save_api_url = "http://hr-system.test/api/store-predictions"  # Laravel API to store predictions

try:
    save_response = requests.post(save_api_url, json=result, timeout=10)
    save_response.raise_for_status()
    print("Predictions successfully stored in Laravel.")
except requests.exceptions.RequestException as e:
    print(f"Error saving predictions: {e}")
