import sqlalchemy
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
        """Create the SQLAlchemy engine and session factory."""
        self.engine = create_engine(
            f'mysql+pymysql://{self.user}:{self.password}@{self.host}/{self.database}'
        )
        self.Session = sessionmaker(bind=self.engine)

    def execute_query(self, query, params=None):
        """Execute a given SQL query with optional parameters."""
        if self.engine is None:
            raise Exception("Database engine is not connected. Call connect() first.")
        with self.engine.connect() as connection:
            if params:
                result = connection.execute(sqlalchemy.text(query), params)
            else:
                result = connection.execute(sqlalchemy.text(query))
            return result

    def dispose(self):
        """Dispose the engine connection pool."""
        if self.engine:
            self.engine.dispose()
