from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker

class MySQLConnector:
    def __init__(self, user, password, host, database):
        self.user = user
        self.password = password
        self.host = host
        self.database = database
        self.engine = None
        self.Session = None

    def connect(self):
        try:
            self.engine = create_engine(f'mysql+pymysql://{self.user}:{self.password}@{self.host}/{self.database}')
            self.Session = sessionmaker(bind=self.engine)
            print("Database connection established.")
        except Exception as e:
            print(f"Error connecting to the database: {e}")

    def execute_query(self, query, params=None):
        session = self.Session()
        try:
            result = session.execute(query, params)
            session.commit()
            return result
        except Exception as e:
            print(f"Error executing query: {e}")
            session.rollback()
        finally:
            session.close()

    def close(self):
        if self.engine:
            self.engine.dispose()
            print("Database connection closed.")