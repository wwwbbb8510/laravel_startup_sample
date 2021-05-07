## Project Briefing

This project is sample project to show how to start up an API project based on Laravel. We create a CRUD set of endpoints for inventory management, i.e. adding, updating, deleting and fetching an item.

## List of APIs

- The following is a list of APIs provided by this project. 

| Verb   | URI              | Action  | Route Name    |
|--------|------------------|---------|---------------|
| GET    | /items           | index   | items.index   |
| GET    | /items/{item_id} | show    | items.show    |
| POST   | /items           | store   | items.store   |
| PUT    | /items/{item_id} | update  | items.update  |
| DELETE | /items/{item_id} | destroy | items.destroy |

- When developing the above APIs, Laravel has provided good tools to generate the skeleton. 

```
# generate the API controller
php artisan make:controller ItemController --api
# generate the ORM model
php artisan make:model -msf
# generate the resource, which is used to format the response of an item
php artisan make:resource ItemResource
# generate the resource collection, which is used to format the response of a list of items
php artisan make:resource ItemCollection --collection
```

- An error handling example is done, which is to handle the exception of not being able to find the corresponding item. Please check `render` function in `App\Exceptions\Handler` class for details. 

## Authentication

- User management:

    A simple Bearer token authentication is set up by using Laravel Sanctum. Laravel/breeze is used to provide a UI for user registration and management. After laravel/breeze is installed, run `npm install && npm run dev` to compile the frontend.

    The registration URL is http(s)://{host}:{port}/register. A sample registration URL on local environment is http://localhost/register

- Token creation API:

    A separate API is provided to create a token by passing the user credentials (where the user has to be created in the **User Management** first).
    
    The token creation API URL on local environment: http://localhost/api/tokens/create. The API details are show below, where the email and password in the `payload` are obvious, and the `token_name` is the app name.
    
    | Verb   | URI              | Payload    |
    |--------|------------------|------------|
    | POST    | /tokens/create  |{"email":"xxx@gmail.com", "password": "xxx", "token_name": "test_app"}|

## Run the project on local environment using Laravel sail package

- Prerequisites: `composer`, `docker`, `docker-compose` need to be installed first. 

- Copy the env file from `env.sail`: `cp env.sail .env`. 

- Run `composer install --dev` to install required packages. `sail` is one of them, which is extremely convenient for local environment.

- Run `./vendor/bin/sail up` to start the docker containers defined in `docker-compose.yml`. 

- Run `./vendor/bin/sail artisan migrate` to create and update database tables. 

- Run `./vendor/bin/sail test --testsuite=Feature` to perform Feature test cases. Note: as the sample project is not very complex, only feature test cases are implemented. For more complex project, unit tests, database tests, and other integration tests may be required.  

## Check the authentication and the APIs using Postman

- Prerequisites: `Postman` needs to be installed first. 

- Open `Postman`, and import `postman.json` in the root folder of this project.

- Open the user registration URL: http://localhost/register in a browser, and finish the user registration.

- Switch back to Postman, click `TokenCreate` request under `laravel_startup_sample` collection. Change the email and password to the ones used in the user registration. And then run the request to get an API token. 

- After the `TokenCreate` step, the token is set up for all the API requests under `laravel_startup_sample` collection. Now, the other API requests in `Postman` can be played. 

**Gotcha**: `{item_id}` for the following APIs -- `items.show, items.update, items.destroy` needs to be a valid `id` of the item in the database. The recommended way of playing the APIs through `Postman` is to call `items.store` API first to create several items, and keep the `ids` for other APIs. 

## Run the project in a production-ready container

**Declaration**: Prod docker configurations are copied, and then modified and tested

- Overview of the production docker: We've copied a set of widely-available docker configurations from the internet, and then we have fine-tuned and tested the configurations to fit the sample project. The entry point of the production docker configurations is `docker-compose-prod.yml`. 

- Copy the env file from `env.prod`: `cp env.prod .env`. 

- Build the `app` image by running `docker-compose -f docker-compose-prod.yml build app`

- Start the containers: `docker-compose -f docker-compose-prod.yml up -d`. 

- Run the following commands to get the required packages, the db migration, and the key generated. 

  ```
  docker-compose -f docker-compose-prod.yml exec app composer install
  docker-compose -f docker-compose-prod.yml exec app php artisan migrate
  docker-compose -f docker-compose-prod.yml exec app php artisan key:generate
  ```

- Open the URL http://localhost:8000/register to register a user. And then follow the same steps of the local environment to play with the APIs. 

- To test APIs in `Postman`, the URLs in the imported `postman.json` need to be changed from http://localhost to http://localhost:8000. 

## Potential Improvements for commercial projects

- Search for item.index API: Currently, item.index API returns all the items with pagination. It should be able to accept some parameters as the search condition. For example, search items with low stocks (stock<5). But this depends on specific requirements.

- Fine-grained permission control for users. Laravel Sanctum allows us to assign `abilities` to tokens, where `abilities` could represent a group of permissions. This can be well used to achieve the goal of a fine-grained access control of APIs. 

- Basic validations have been added for the sample project, but for real projects, the validation should be more thorough depending on the specific requirements. 

- Logging should be proper designed or implemented for production. For example, a third-party tool such as Datadog or Splunk could be used to receive the logs instead of local logs. 

- A cache layer could be added to improve the performance. Depending on specific projects, if the performance is a key metric, a cache layer should be added on top of the database, and redis is my preferred solution. 

- CloudFormation or Terraform could be used to automate the infrastructure deployment. If it's only for AWS, CloudFormation could be a good choice. Otherwise, Terraform may be preferred. 

- Below are also a list of considerations for commercial projects: 
    - Group APIs: Since the sample project only has one set of APIs, for complex projects, the APIs should be grouped, e.g. User-related API group, Inventory-related API group. 
    - API versioning: API versioning is not implemented in the sample project. But before starting a project, a proper versioning strategy needs to be designed.
    - Fallback routes: Customised fallback routes need to be defined for each set of APIs.
    - Rate Limits: To reduce the lost of being attacked, rate limits need to be added for APIs. 

## Tips for font-end applications to call the APIs mainly about security considerations

- If possible, a Single Page Application, which resides in the same top-level domain of the API project, should be the preferred approach of building the front-end application.

    - Sanctum SPA authentication can be used to tighten the security. 

    - On production environment, a whitelisted domains should be defined for SPA authentication. 

    - CSRF should be added with the frontend by using sanctum/csrf-cookie. 

- To benefit the performance, front-end should store and maintain the model if possible, and only communicate with backend APIs to sync the model when necessary. 

- If token has to be used for the API authentication, Frontend should refresh the token frequently in order to protect token forgery. 

- When calling the API, make suer `Accept: 'application/json'` is set in the request header. This could prevent errors causes by redirection. For example, the sample project may return a login html page if the token is correct. 

- Whenever frontend needs to call backend APIs, frontend should take rate limits as a consideration. This could add another layer of preventing malicious attacks from the frontend. 

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
