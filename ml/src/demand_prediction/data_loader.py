import pandas as pd
import numpy as np

def load_data(file_path):
    """
    Load the demand prediction dataset from a CSV file.
    
    Parameters:
    file_path (str): The path to the dataset file.
    
    Returns:
    pd.DataFrame: A DataFrame containing the loaded data.
    """
    data = pd.read_csv(file_path)
    return data

def preprocess_data(data):
    """
    Preprocess the demand prediction data.
    
    Parameters:
    data (pd.DataFrame): The raw data to preprocess.
    
    Returns:
    pd.DataFrame: A DataFrame containing the preprocessed data.
    """
    # Handle missing values
    data.fillna(method='ffill', inplace=True)
    
    # Convert date column to datetime
    data['date'] = pd.to_datetime(data['date'])
    
    # Set date as index
    data.set_index('date', inplace=True)
    
    # Resample data to weekly frequency
    data = data.resample('W').sum()
    
    return data

def split_data(data, train_size=0.8):
    """
    Split the data into training and testing sets.
    
    Parameters:
    data (pd.DataFrame): The preprocessed data.
    train_size (float): The proportion of the data to use for training.
    
    Returns:
    tuple: A tuple containing the training and testing data.
    """
    train_size = int(len(data) * train_size)
    train, test = data[:train_size], data[train_size:]
    return train, test