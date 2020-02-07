var app = {
    domain: undefined,
    accounts: [],
    photos: []
};

app.start = async function () {

    // Init
    app.accounts = [
        { first_name: "Артём", surname: "Лебедев", password: "Kurgan2020" },
        { first_name: "Андрей", surname: "Яковлев", password: "Kurgan2020" },
        { first_name: "Павел", surname: "Анисимов", password: "Kurgan2020" },
        { first_name: "Роман", surname: "Водеников", password: "Kurgan2020" },
        { first_name: "Алексей", surname: "Никишин", password: "Kurgan2020" }
    ];

    app.domain = undefined;
    app.photos = [];

    // Очистка данных
    $('.signup').html("");
    $('.accounts').html("");
    $('.login').html("");
    $('.logout').html("");
    $('.photoupload').html("");
    $(".photoedit").html("");
    $(".usernoauth").html("");
    $(".photoget").html("");
    $(".photodelete").html("");
    $('#log').html("");
    $("#cors").html(`<div class="text-center alert alert-primary" role="alert">Ожидание.</div>`);

    app.users(); // render users

    // Получаем URL
    let el = $('#exampleInputEmail1');
    if(!el.val()) return alert("Ошибка, домен не обнаружен.");
    app.domain = el.val();
    

};

// Логирование.
app.log = (name, data) => $("#log").append(name + "<hr />" + data);

// Проверка на CORS.
app.cors = async function () {
    return new Promise(function(resolve, reject) {
        app.log("CORS", "Отправка запроса");
        $("#cors").html(`<div class="text-center alert alert-primary" role="alert">Отправка запроса.</div>`);
        $.post("/php/cors.php", { url: app.domain }, (data) => {
            if(data.success) {
                app.log("cors", "Успешно получен.");
                $("#cors").html(`<div class="text-center alert alert-success" role="alert">Успешно получен!</div>`);
            } else {
                $("#cors").html(`<div class="text-center alert alert-danger" role="alert">Не обнаружен.</div>`);
                app.log("cors", "Не обнаружен.");
            }
            // Решение...
            setTimeout(() => resolve(true), 2000);
        }).fail(() => {
            app.log("cors", "Ошибка отправки запроса. Необходима ручная проверка.");
            $("#cors").html(`<div class="text-center alert alert-danger" role="alert">Ошибка отправки запроса. Необходима ручная проверка.</div>`);
            resolve(false);
        });
    });
};

// Не авторизованному пользователю не доступен функционал.
app.authenfication = function () {
    return new Promise(function(resolve, reject) {
        app.log("Не авторизованному пользователю не доступен функционал.", "Отправка запроса");
        $.post("/php/authenfication.php", { url: app.domain }, (data) => {
            app.log("Не авторизованному пользователю не доступен функционал.", "Успешное получение данных");
            for(let i in data) {
                let status = { type: "success", message: "Пройдено!" };
                if(data[i].code != 422) status = { type: "warning", message: "Не пройдено!" };
                $('.usernoauth').append(`<tr>
                    <th scope="row">${ i }</th>
                    <td>${ data[i].name || "Неизвестно" }</td>
                    <td>${ data[i].response.message || "Без данных" }</td>
                    <td>${ data[i].code }</td>
                    <td class="text-${ status.type } font-weight-bold">${ status.message }</td>
                </tr>`);
                app.log(data[i].name + " код ответа " + data[i].code, JSON.stringify(data[i].response));
            }
            setTimeout(() => resolve(true), 4000);
        }).fail(() => {
            app.log("Не авторизованному пользователю не доступен функционал.", "Ошибка отправки запроса. Необходима ручная проверка.");
            resolve(false);
        });
    });
};

// Валидация регистрации без обязательных полей.
app.signup_nodata = function () {
    return new Promise(function(resolve, reject) {
        app.log("Валидация регистрации без обязательных полей.", "Отправка запроса");
        $.post("/php/signup.php", { url: app.domain }, (data) => {
            
            app.log("Валидация регистрации без обязательных полей.", "Обработка запроса");

            let success = true, message_notify = "success", message = "Пройдено!";
            if(data.code != 422) success = false;
            
            // Проверка структуры
            for(var i in data.response) if(i != "first_name" && i != "surname" && i != "phone" && i != "password") status = false;

            // Ошибочное уведомление
            if(!success) {
                message_notify = "danger";
                message = "Ошибка проверки";
            }

            // Вывод
            $(".signup").append(`<tr>
                <th scope="row">1</th>
                <td>Валидация</td>
                <td>Проверка на обязательные данные</td>
                <td>${ data.code }</td>
                <td class="text-${ message_notify } font-weight-bold">${ message }</td>
            </tr>`);

            app.log("Валидация регистрации без обязательных полей. код ответа " + data.code, JSON.stringify(data.response));
            setTimeout(() => resolve(true), 2000);
        }).fail(() => {
            app.log("Валидация регистрации без обязательных полей.", "Ошибка отправки запроса. Необходима ручная проверка.");
            resolve(false);
        });
    });
};

// Номер меньше 11 символов.
app.signup_number_1 = function () {
    return new Promise(function(resolve, reject) {
        app.log("Валидация номер меньше 11 символов.", "Отправка запроса");
        $.post("/php/signup.php", { url: app.domain }, (data) => {
            
            app.log("Валидация номер меньше 11 символов.", "Обработка запроса");

            let success = true, message_notify = "success", message = "Пройдено!";
            if(data.code != 422) success = false;
            
            // Проверка структуры
            for(var i in data.response) if(i != "phone") status = false;

            // Ошибочное уведомление
            if(!success) {
                message_notify = "danger";
                message = "Ошибка проверки";
            }

            // Вывод
            $(".signup").append(`<tr>
                <th scope="row">2</th>
                <td>Валидация</td>
                <td>Номер меньше 11 символов.</td>
                <td>${ data.code }</td>
                <td class="text-${ message_notify } font-weight-bold">${ message }</td>
            </tr>`);

            app.log("Валидация номер меньше 11 символов. код ответа " + data.code, JSON.stringify(data.response));
            setTimeout(() => resolve(true), 2000);
        }).fail(() => {
            app.log("Валидация номер меньше 11 символов.", "Ошибка отправки запроса. Необходима ручная проверка.");
            resolve(false);
        });
    });
};

// Номер больше 11 символов.
app.signup_number_2 = function () {
    return new Promise(function(resolve, reject) {
        app.log("Валидация номер больше 11 символов.", "Отправка запроса");
        $.post("/php/signup.php", { url: app.domain }, (data) => {
            
            app.log("Валидация номер больше 11 символов.", "Обработка запроса");

            let success = true, message_notify = "success", message = "Пройдено!";
            if(data.code != 422) success = false;
            
            // Проверка структуры
            for(var i in data.response) if(i != "phone") status = false;

            // Ошибочное уведомление
            if(!success) {
                message_notify = "danger";
                message = "Ошибка проверки";
            }

            // Вывод
            $(".signup").append(`<tr>
                <th scope="row">3</th>
                <td>Валидация</td>
                <td>Номер больше 11 символов.</td>
                <td>${ data.code }</td>
                <td class="text-${ message_notify } font-weight-bold">${ message }</td>
            </tr>`);

            app.log("Валидация номер больше 11 символов. код ответа " + data.code, JSON.stringify(data.response));
            setTimeout(() => resolve(true), 2000);
        }).fail(() => {
            app.log("Валидация номер больше 11 символов.", "Ошибка отправки запроса. Необходима ручная проверка.");
            resolve(false);
        });
    });
};

// Успешная регистрация
app.signup_success = function (account) {
    return new Promise(function(resolve, reject) {
        app.log("Успешная регистрация.", "Отправка запроса");
        $.post("/php/signup.php", { 
            url: app.domain,
            phone: app.accounts[account].phone,
            first_name: app.accounts[account].first_name,
            surname: app.accounts[account].surname,
            password: app.accounts[account].password
        }, (data) => {
            
            app.log("Успешная регистрация.", "Обработка запроса на регистрацию аккаунта");

            let success = true, message_notify = "success", message = "Пройдено!";
            if(data.code != 201) success = false;
            
            // Проверка структуры
            for(var i in data.response) if(i != "id") status = false;

            // Ошибочное уведомление
            if(!success) {
                message_notify = "danger";
                message = "Ошибка проверки";
            }

            // Вывод
            if(!app.accounts[0].id) $(".signup").append(`<tr>
                <th scope="row">4</th>
                <td>Регистрация</td>
                <td>Успешная регистрация</td>
                <td>${ data.code }</td>
                <td class="text-${ message_notify } font-weight-bold">${ message }</td>
            </tr>`);

            if(data.response.id) app.accounts[account].id = data.response.id;
            app.users(); // render users

            app.log("Успешная регистрация. код ответа " + data.code, JSON.stringify(data.response));
            setTimeout(() => resolve(true), 2000);
        }).fail(() => {
            app.log("Успешная регистрация.", "Ошибка отправки запроса. Необходима ручная проверка.");
            resolve(false);
        });
    });
};

// Валидация авторизации без обязательных полей.
app.login_nodata = function () {
    return new Promise(function(resolve, reject) {
        app.log("Валидация авторизации без обязательных полей", "Отправка запроса");
        $.post("/php/login.php", { url: app.domain }, (data) => {
            
            app.log("Валидация авторизации без обязательных полей", "Обработка запроса");

            let success = true, message_notify = "success", message = "Пройдено!";
            if(data.code != 422) success = false;
            
            // Проверка структуры
            for(var i in data.response) if(i != "phone" && i != "password") status = false;

            // Ошибочное уведомление
            if(!success) {
                message_notify = "danger";
                message = "Ошибка проверки";
            }

            // Вывод
            if(!app.accounts[0].id) $(".login").append(`<tr>
                <th scope="row">1</th>
                <td>Валидация</td>
                <td>Отправка без обязательных полей</td>
                <td>${ data.code }</td>
                <td class="text-${ message_notify } font-weight-bold">${ message }</td>
            </tr>`);

            app.log("Валидация авторизации без обязательных полей. код ответа " + data.code, JSON.stringify(data.response));
            setTimeout(() => resolve(true), 2000);
        }).fail(() => {
            app.log("Валидация авторизации без обязательных полей.", "Ошибка отправки запроса. Необходима ручная проверка.");
            resolve(false);
        });
    });
};

// Успешная авторизация 
app.login_success = function (account) {
    return new Promise(function(resolve, reject) {
        app.log("Успешная авторизация", "Отправка запроса");
        $.post("/php/login.php", { url: app.domain, phone: app.accounts[account].phone, password: app.accounts[account].password }, (data) => {
            
            app.log("Успешная авторизация", "Обработка запроса");

            let success = true, message_notify = "success", message = "Пройдено!";
            if(data.code != 422) success = false;
            
            // Проверка структуры
            for(var i in data.response) if(i != "token") status = false;

            // Ошибочное уведомление
            if(!success) {
                message_notify = "danger";
                message = "Ошибка проверки";
            }

            // Вывод
            if(!app.accounts[0].token) $(".login").append(`<tr>
                <th scope="row">2</th>
                <td>Авторизация</td>
                <td>Успешная авторизация</td>
                <td>${ data.code }</td>
                <td class="text-${ message_notify } font-weight-bold">${ message }</td>
            </tr>`);

            if(data.response.token) accounts[account].token = data.response.token;
            app.users();

            app.log("Валидация успешной авторизации. код ответа " + data.code, JSON.stringify(data.response));
            setTimeout(() => resolve(true), 2000);
        }).fail(() => {
            app.log("Валидация успешной авторизации.", "Ошибка отправки запроса. Необходима ручная проверка.");
            resolve(false);
        });
    });
};

// Успешный выход

// Валидация смены токена на выходе.

// Генерация номера телефона.

// Рендер пользователей
app.users = function () {
    $('.accounts').html("");
    app.accounts.forEach(element => {
        $('.accounts').append(`<tr>
            <th scope="row">${ element.id || "..." }</th>
            <td>${ element.first_name || "..." }</td>
            <td>${ element.surname || "..." }</td>
            <td>${ element.phone || "..." }</td>
            <td>${ element.password || "..." }</td>
            <td>${ element.token || "..." }</td>
        </tr>`);
    });
};