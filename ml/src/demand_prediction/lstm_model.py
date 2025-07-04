from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense, Dropout
from tensorflow.keras.callbacks import EarlyStopping
import numpy as np

class LSTMModel:
    def __init__(self, input_shape):
        self.model = Sequential()
        self.model.add(LSTM(50, return_sequences=True, input_shape=input_shape))
        self.model.add(Dropout(0.2))
        self.model.add(LSTM(50, return_sequences=False))
        self.model.add(Dropout(0.2))
        self.model.add(Dense(25))
        self.model.add(Dense(1))
        self.model.compile(optimizer='adam', loss='mean_squared_error')

    def train(self, X_train, y_train, epochs=50, batch_size=32):
        early_stopping = EarlyStopping(monitor='loss', patience=5)
        self.model.fit(X_train, y_train, epochs=epochs, batch_size=batch_size, callbacks=[early_stopping])

    def evaluate(self, X_test, y_test):
        return self.model.evaluate(X_test, y_test)

    def predict(self, X):
        return self.model.predict(X)