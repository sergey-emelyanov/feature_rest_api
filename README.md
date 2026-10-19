## REST API для управления списком задач


***


### Технологии использованные в проекте 
- Laravel (v11.x)
- PHP (v8.4)
- MySQL(v8.0)
- Nginx
- Docker / Docker Compose

### Устанока и запуск проекта 

1. Склонировать репозиторий 

```bash
mkdir task_api
cd /task_api
git clone https://github.com/sergey-emelyanov/feature_rest_api.git .
```

2. Скопировать файл окружения 

```bash
cp .env.example .env
```

3. Поднять контейнер

```bash 
docker compose up -d --build
```

4. Установить зависимости Composer
```bash
docker compose exec app composer install
```

5. Сгенерировать ключ
```bash
docker compose exec app php artisan key:generate
```

6. Выполнить миграции
```bash 
docker compose exec app php artisan migrate
```

7. Сгенерировать документацию Swagger
```bash
docker compose exec app php artisan l5-swagger:generate
```


### Документация API 

После успешного старта приложения документация будет доступна по адресу 

http://localhost:8876/api/documentation


### Тесты 

Для проекта были написаны функциональные тесты на позитивный и негативный сценарий

1. Позитивный 
   - Получение списка задач с проверкой количества
   - Создание задачи с проверкой статуса 201 и наличия записи в БД
   - Получение одной задачи
   - Обновление задачи (проверка изменения данных в БД)
   - Удаление задачи проверка статуса 204

2. Негативный 
   - 422 Проверка того, что при отправке невалидных данных api возвращает корректные ошибки валидации
   - 404 Проверка того, что при обращении к несуществующей задаче api отдает 404 ошибку, а не падает в 500 

Для написания тестов использовал встроенный фреймворк PHPunit,  Faker и Eloquent Factory для создания тестовых данных. 
Сами тесты можно запустить данной командой

```bash
docker compose exec app php artisan test
```

А так же были задействованы GithubActions, которые запускают тесты при каждом пуше в ветку main. 


### Деплой 

Реализован мануальный деплой проекта, посмотреть можно 

http://38.180.40.10:8876/api/documentation/

