# Backend: Referencia

#### Estructura de la DB

La tabla **weather_info**  cuenta con la siguiente estructura:

```sql
CREATE TABLE weather_info (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    location VARCHAR(100) UNIQUE NOT NULL,
    temperature DECIMAL(8, 4),
    temperatureApparent DECIMAL(8, 4),
    precipitationProbability DECIMAL(8, 4),
    humidity DECIMAL(8, 4),
    windSpeed DECIMAL(8, 4),
    date_creation DATETIME
);
```

