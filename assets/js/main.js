const locationInput = document.getElementById("location-input");
const btnCache = document.getElementById("fetch-cache");
const btnNewest = document.getElementById("fetch-newest");
const errorsPlace = document.getElementById("errors-place");
const dataPlace = document.getElementById("data-place");
const responseTime = document.getElementById("response-time");

const deleteErrors = () => errorsPlace.innerHTML = ``;
const errorOcurred = (error = "Some error ocurred, please, try again later") => {
    errorsPlace.innerHTML = `
    <div class="errors__error">
        <span>${error}</span>
        <button class="errors__btn" data-delete-error><i data-delete-error class="fa-solid fa-xmark"></i></button>
    </div>
    `;
}

const insertData = data => {
    const date_creation = data["date_creation"].slice(0, -3);
    dataPlace.innerHTML = `
    <div class="data__info"><h5 class="data__info--h5">Info last update</h5><p class="data__info--p">${date_creation}</p></div>
    <div class="data__info"><h5 class="data__info--h5">Temperature</h5><p class="data__info--p">${data["temperature"]}°C</p></div>
    <div class="data__info"><h5 class="data__info--h5">Precipitation Probability</h5><p class="data__info--p">${data["precipitationProbability"]}%</p></div>
    <div class="data__info"><h5 class="data__info--h5">Temperature Apparent</h5><p class="data__info--p">${data["temperatureApparent"]}°C</p></div>
    <div class="data__info"><h5 class="data__info--h5">Humidity</h5><p class="data__info--p">${data["humidity"]}%</p></div>
    <div class="data__info"><h5 class="data__info--h5">Wind Speed</h5><p class="data__info--p">${data["windSpeed"]}m/s</p></div>
    `;
}

async function fetchData(url, btn) {
    const location = locationInput.value;
    if (location?.length < 3 || (/[\.\,\\$\"\'\/\_\-\<\>]/).test(location) )
        return errorOcurred("You must give a valid location (use your real location)");

    try {
        btn.classList.add("loading");

        const startTime = Date.now();
        const data = await fetch(url + `?location=${location}`).then(res => res.json());

        const res_time = Date.now() - startTime;
        btn.classList.remove("loading");

        if (!data["ok"]) return errorOcurred(data["message"]);
        insertData(data["data"]);

        responseTime.textContent = `${res_time}ms`;
    } catch {
        btn.classList.remove("loading");
        errorOcurred();
    }
}

btnCache.addEventListener("click", () => fetchData("/get/cache", btnCache));
btnNewest.addEventListener("click", () => fetchData("/get/newest", btnNewest));

errorsPlace.addEventListener("click", ({target}) => ("deleteError" in target.dataset) && deleteErrors())
