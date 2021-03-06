## Модуль интеграции с CMS OpenCart  3.0.0

Данный модуль обеспечивает взаимодействие между интернет-магазином на базе CMS Opencart версии 3.0.x и сервисом платежей [ХуткiГрош](hutkigrosh.by)
  * Модуль интеграции для версии [OpenCart 1.5.x](https://github.com/esasby/hgrosh/tree/master/CMS/Plugins/OpenCart/1.5.5%20(http))
  * Модуль интеграции для версии [OpenCart 2.1.x](https://github.com/esasby/hgrosh/tree/master/CMS/Plugins/OpenCart/2.1)
  * Модуль интеграции для версии [OpenCart 2.2.x](https://github.com/esasby/hgrosh/tree/master/CMS/Plugins/OpenCart/2.2)
  * Модуль интеграции для версии [OpenCart 2.3.x](https://github.com/esasby/hgrosh/tree/master/CMS/Plugins/OpenCart/2.3)

### Инструкция по установке:
1. Создайте резервную копию вашего магазина и базы данных
2. Установите модуль [opencart30-hutkigrosh-payment-module.ocmod.zip](https://github.com/esasby/hgrosh/blob/master/CMS/Plugins/OpenCart/3.0/opencart30-hutkigrosh-payment-module.ocmod.zip) с помощью _Модули_ -> _Установка расширений_
3. Напротив модуля ХуткiГрош нажмите «Установить», а затем «Изменить».
4. Заполните параметры для идентификации вашего магазина в системе ХуткiГрош
    * Уникальный идентификатор услуги ЕРИП – ID ЕРИП услуги
    * Логин интернет-магазина – логин в системе ХуткiГрош.
    * Пароль интернет-магазина – пароль в системе ХуткiГрош.
    * Путь в дереве ЕРИП - путь для оплаты счета в дереве ЕРИП, который будет показан клиенту после оформления заказа (например, Платежи > Магазин > Заказы)     
5. В выпадающем списке «Статус» выберите «Включено».
6. Сохраните изменения.

### Внимание!
Для автоматического обновления статуса заказа (после оплаты клиентом выставленного в ЕРИП счета) необходимо сообщить в службу технической поддержки сервиса «Хуткi Грош» адрес обработчика:
```
http://mydomen.my/index.php?route=extension/payment/hutkigrosh/notify
```

### Тестовые данные
Для настрой оплаты в тестовом режиме:
 * воспользуйтесь данными для подключения к тестовой системе, полученными при регистрации в ХуткiГрош
 * включите в настройках модуля режим "Песочницы" 
 * для эмуляции оплаты клиентом выставленного счета воспльзуйтесь личным кабинетом [тестовой системы](https://trial.hgrosh.by) (меню _Тест оплаты ЕРИП_)

_Разработано и протестировано с OpenCart v.3.0.0.2_


