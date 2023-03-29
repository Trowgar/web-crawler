const weatherPath = document.querySelector('script[src$="dashboard.js"]').getAttribute('data-path');
const citiesPath = document.querySelector('script[src$="dashboard.js"]').getAttribute('data-cities-path');

async function loadCities() {
    const response = await fetch(citiesPath);
    if (!response.ok) {
        throw new Error(`Error fetching cities data: ${response.statusText}`);
    }
    return response.json();
}

async function createButtons() {
    const cities = await loadCities();
    const buttonsContainer = document.getElementById('weather-buttons');

    cities.forEach(city => {
        const todayButton = document.createElement('button');
        todayButton.textContent = `${city.name} weather today`;
        todayButton.addEventListener('click', () => fetchData(city.name, 'today'));
        buttonsContainer.appendChild(todayButton);

        const averageButton = document.createElement('button');
        averageButton.textContent = `${city.name} weather 7 days ago`;
        averageButton.addEventListener('click', () => fetchData(city.name, 'last_7_days'));
        buttonsContainer.appendChild(averageButton);
    });
}

function fetchData(city, mode, daysAgo = 0) {
    fetch(`${weatherPath}?city=${city}&mode=${mode}&daysAgo=${daysAgo}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error fetching weather data: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('date').textContent = `Date: ${data.date}`;
            document.getElementById('temperature').textContent = `Temperature: ${data.temperature}°C`;
            document.getElementById('wind-speed').textContent = `Wind Speed: ${data.wind_speed} km/h`;
            document.getElementById('weather-data').style.display = 'block';
        })
        .catch(error => {
            console.error(error);
        });
}

createButtons();
