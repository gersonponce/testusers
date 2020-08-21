

## Acerca de prueba de usuarios

La aplicacion está desarrollada en vue y laravel
## Live demo 
[http://testuser.gersonponce.com/](http://testuser.gersonponce.com)
## Instalar
- Clonar del repositorio 
```
git clone https://github.com/gersonponce/testusers.git
```
- Ejecutar los siguientes comandos

```
composer install
npm install
npm run dev
```
- Para crear la tabla y crear los 20,000 registros

```
php artisan migrate:fresh --seed
```


## Componentes usados
- Vue good table
- Vue Bootstrap
- Axios
