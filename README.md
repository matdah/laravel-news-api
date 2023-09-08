# Laravel News API
Ett enklare API för att hantera nyheter, med auth - register/login med plain text token.

## Installation
* Ändra inställningarna i .env-filen, alternativ skapa samma db/user/pass
* Kör: composer install
* Kör: php artisan key:generate
* Kör: php artisan migrate
* Starta med: php artisan serve
Körs på port: 8000

## Tabell: news
Lagrar:
* id
* title
* content
* image 
* users_id (id i users-tabellen)
* created at
* updated at

Notering: "author" läggs till vid utläsning, med namn på den som skapade posten.
Notering 2: fullständig sökväg läggs till för uppladdade bildfiler.

## Routes
/api/register - registrerar konto (returnerar konto + token)
```json
{
    "name" : "Mattias Dahlgren",
    "email" : "mattias.dahlgren@miun.se",
    "password" : "password"
}
```

/api/login - loggar in (returnerar konto + token)
```json
{
    "email" : "mattias.dahlgren@miun.se",
    "password" : "password"
}
```

/api/logout - loggar ut användare (utifrån Token)


GET: /api/news -> Lista med nyheter (lägger till author med namn) 
GET: /api/news/id -> specifik post utifrån id

POST: /api/news -> postar nytt inlägg
Kräver title och content, image är valfri

PUT: /api/news -> uppdaterar befintlig inlägg 
Går ej att uppdatera bild vid update, eller ändra id för den som skapat posten ursprungligen

DELETE: /api/news/id -> raderar specifik post