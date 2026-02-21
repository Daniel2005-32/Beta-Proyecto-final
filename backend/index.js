require('dotenv').config();
const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

const db = mysql.createConnection({
    host: 'localhost', // Cambiar a 'mysql' si se corre dentro de Docker
    port: process.env.DB_PORT || 3307,
    user: process.env.DB_USERNAME || 'Daniel',
    password: process.env.DB_PASSWORD || 'DanielSM',
    database: process.env.DB_DATABASE || 'proyecto_final'
});

db.connect((err) => {
    if (err) {
        console.error('Error conectando a la base de datos:', err);
        return;
    }
    console.log('Conectado a MySQL');
});

app.get('/api/items', (req, res) => {
    db.query('SELECT * FROM items', (err, results) => {
        if (err) return res.status(500).json(err);
        res.json(results);
    });
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Servidor backend corriendo en http://localhost:${PORT}`);
});
