
# Finito

## About Laravel-Finito Cloud System

Banking system like no other, make secure transactions online or offline using our app or transfer device.

## Live Demo

- [https://finito-cloud.herokuapp.com/](https://finito-cloud.herokuapp.com).

## API EndPoints

1. Users can create an account and a welcome email sent to them upon registration.
- [https://finito-cloud.herokuapp.com/api/register](https://finito-cloud.herokuapp.com/api/register)


```
Payload: {
    "name" : "",
    "email" : "",
    "password" : ""
}
```


2. Users can fund their wallets using their atm cards.
- [https://finito-cloud.herokuapp.com/api/wallet/credit](https://finito-cloud.herokuapp.com/api/wallet/credit)


```
Payload: {
    "amount" : "",
    "description" : ""
}
```

3. Users can fund other users wallets using a unique identifier that each user has.
- [https://finito-cloud.herokuapp.com/api/wallet/transfer](https://finito-cloud.herokuapp.com/api/wallet/transfer)


```
Payload: {
    "to_user_id" : "",
    "amount" : "",
    "description" : ""
}
```


## Built with Laravel
Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

This is why we choosed it.