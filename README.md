# Golos.Joomla

Компонент работает в связке с модулем, модуль без компонента сможет работать, только если на странице подключен golos js api.

Дополнительной установки и настройки не требуется. В качестве логина используется логин на GOLOS, в качестве пароля Posting Key. Ключ хранится локально в LocalStorage браузера в зашифрованном виде. На сайте хранится только хэш пароля для авторизации. 
Все операции с приватным ключом должны производиться по возможности на стороне клиента с использованием браузерного JS.

При первой авторизации, для проверки ключа, им подписывается транзакция follow. Если она срабатывает, то ключ принимается. В данном коде это подписка на @golos.world, вы можете поменять ее на аккаунт своего проекта. 
