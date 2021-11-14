<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Restful web service</h1>
    <br>
</p>

### Задание

Разработать RESTful web-сервис управления задачами, который должен соответствовать требованиям:
- Определение клиента по токену.
- Создание задачи. На вход передается имя задачи (максимальная длина значения 256 символов, значение уникальное). Задаче присваивается статус "Новая" и она закрепляется за клиентом. Сервис отвечает уникальным идентификатором задачи или сообщением об ошибке, в случае, если создание не удалось.
- Удаление задачи. Удалять можно только задачи, созданные клиентом и только в статусе "Новая". Сервис возвращает статус операции или ошибку, если удаление не удалось.
- Получение конкретной задачи. При получении задачи по идентификатору её статус изменяется на "В работе". Сервис возвращает подтверждение изменения статуса, или ошибку в случае, если задача не найдена.
- Закрытие задачи с отправкой результата. На вход передается какой-либо текстовый результат. Задаче присваивается статус "Выполнена" или "Ошибка", если в передаваемом результате есть вхождение "ERROR: ", который возвращается клиенту.
- Получение списка задач с фильтром по статусу (опционально) и пагинацией для клиента.

### Решение 
Решение задачи представлено в данном репозитории.  
Для реализации RESTful web-сервиса использовался фреймворк Yii2 basic.  
В качестве метода авторизации выбран метод HttpBearerAuth.

#### Токены авторизации пользователей
Токены авторизации пользователей задаются в файле [models/User.php](models/User.php)  

Для проверки правильности функционирования сервиса можно воспользоваться предустановленным значением токена `102-token` тестового пользователя pavel.

#### Установка 
    composer install
    php yii migrate/up

#### Примеры HTTP запросов 
Cоздание новой задачи:
```http request
### create ###
POST http://restfulweb.test:80/tasks/create
Accept: application/json
Authorization: Bearer 102-token
Content-Type: application/json

{
  "name": "Task13"
}
```

Удаление новой задачи c id=15:
```http request
### delete ###
DELETE http://restfulweb.test:80/tasks/15
Accept: application/json
Authorization: Bearer 102-token
```

Просмотр задачи с id=2:
```http request
### view one task with id=2 ###
GET http://restfulweb.test:80/tasks/2
Accept: application/json
Authorization: Bearer 102-token
```

Просмотра всех задач с постраничным выводом и применением фильтра по статусу (опционально):
```http request
### view all tasks ###
GET http://restfulweb.test:80/tasks?page=0
Accept: application/json
Authorization: Bearer 102-token
Content-Type: application/json

{
  "status": "Новая"
}
```

Закрытие задачи с id=3:
```http request
### close ###
POST http://restfulweb.test:80/tasks/close
Accept: application/json
Authorization: Bearer 102-token
Content-Type: application/json

{
  "id": "3",
  "result": "Результат выполнения задачи №3"
}
```
