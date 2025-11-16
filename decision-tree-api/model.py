"""
Decision Tree Model menggunakan scikit-learn
Algoritma: CART (Classification and Regression Trees)
"""

import pandas as pd
import numpy as np
from sklearn.tree import DecisionTreeClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, classification_report
import pickle
from pathlib import Path


class DecisionTreeModel:
    """Model Decision Tree untuk prediksi area riset"""
    
    def __init__(self):
        self.model = DecisionTreeClassifier(
            criterion='gini',  # CART algorithm
            max_depth=10,
            min_samples_split=5,
            min_samples_leaf=2,
            random_state=42
        )
        self.feature_names = ['algoritma', 'pemrograman', 'basis_data', 'kecerdasan_buatan']
        self.label_encoder = None
        
    def load_training_data(self):
        """Load data training dari CSV"""
        data_path = Path(__file__).parent / "data" / "training_data.csv"
        
        if not data_path.exists():
            # Generate dummy data jika file tidak ada
            return self._generate_dummy_data()
        
        df = pd.read_csv(data_path)
        return df
    
    def _generate_dummy_data(self):
        """
        Generate dummy training data untuk development
        Data ini harus diganti dengan data historis yang sebenarnya
        """
        np.random.seed(42)
        n_samples = 200
        
        # Generate data dummy dengan pola tertentu
        data = {
            'algoritma': np.random.uniform(60, 100, n_samples),
            'pemrograman': np.random.uniform(60, 100, n_samples),
            'basis_data': np.random.uniform(60, 100, n_samples),
            'kecerdasan_buatan': np.random.uniform(60, 100, n_samples),
        }
        
        # Assign kode_area berdasarkan pola nilai
        kode_area = []
        for i in range(n_samples):
            algo = data['algoritma'][i]
            prog = data['pemrograman'][i]
            db = data['basis_data'][i]
            ai = data['kecerdasan_buatan'][i]
            
            # Logika sederhana untuk mapping (dalam production, gunakan data historis)
            if ai > 85 and algo > 80:
                kode_area.append('EXPERT_SYSTEM')
            elif prog > 85 and db > 80:
                kode_area.append('WEB_MOBILE')
            elif algo > 85 and prog > 85:
                kode_area.append('GAME_DEV')
            elif ai > 80 and db > 80:
                kode_area.append('DATA_ANALYTICS')
            elif algo > 75:
                kode_area.append('CYBER_SEC')
            else:
                kode_area.append('NLP')
        
        data['kode_area'] = kode_area
        df = pd.DataFrame(data)
        
        # Simpan dummy data untuk referensi
        data_dir = Path(__file__).parent / "data"
        data_dir.mkdir(exist_ok=True)
        df.to_csv(data_dir / "training_data.csv", index=False)
        
        return df
    
    def train(self):
        """Train model dengan data training"""
        df = self.load_training_data()
        
        # Pisahkan features dan target
        X = df[self.feature_names].values
        y = df['kode_area'].values
        
        # Split data (80% training, 20% testing)
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42, stratify=y
        )
        
        # Train model
        self.model.fit(X_train, y_train)
        
        # Evaluate
        y_pred = self.model.predict(X_test)
        accuracy = accuracy_score(y_test, y_pred)
        
        print(f"Model training completed!")
        print(f"Accuracy: {accuracy:.2%}")
        print(f"\nClassification Report:")
        print(classification_report(y_test, y_pred))
        
        return self
    
    def predict(self, X):
        """Prediksi kode_area untuk input data"""
        if self.model is None:
            raise ValueError("Model belum dilatih. Panggil train() terlebih dahulu.")
        
        return self.model.predict(X)
    
    def predict_proba(self, X):
        """Prediksi probabilitas untuk setiap kelas"""
        if self.model is None:
            raise ValueError("Model belum dilatih. Panggil train() terlebih dahulu.")
        
        return self.model.predict_proba(X)
    
    def save(self, filepath):
        """Simpan model ke file"""
        filepath = Path(filepath)
        filepath.parent.mkdir(parents=True, exist_ok=True)
        
        with open(filepath, 'wb') as f:
            pickle.dump(self, f)
        
        print(f"Model saved to {filepath}")
    
    @classmethod
    def load(cls, filepath):
        """Load model dari file"""
        with open(filepath, 'rb') as f:
            model = pickle.load(f)
        return model

