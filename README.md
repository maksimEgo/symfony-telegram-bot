# Symfony Telegram Bot Builder

**Symfony Telegram Bot Builder** is a powerful tool for creating Telegram bots, built on the Symfony framework and the high-performance RoadRunner server.

## 🛠 Features

- **Symfony Power**: Leverage the full capabilities of the Symfony framework to create complex bots with support for Dependency Injection, routing, events, and more.
- **High Performance**: RoadRunner ensures fast request handling, allowing your bot to respond quickly to user messages.
- **Flexibility**: Easily extendable architecture allows you to add new features and integrations as needed.
- **PSR-7 and PSR-15 Support**: Uses PSR standards for maximum compatibility with other PHP components.

## 🚀 Getting Started

### Requirements

- PHP 8.3 or higher
- Composer
- Symfony 7.1 or higher
- RoadRunner 3.x

### Installation

1. **Clone the repository:**

    ```bash
    git clone https://github.com/maksimEgo/symfony-telegram-bot.git
    cd symfony-telegram-bot
    ```

2. **Install dependencies:**

    ```bash
    composer install
    ```

3. **Configure the environment:**

   Copy the `.env` file and configure the database and other service parameters:

    ```bash
    cp .env.example .env
    ```

4. **Run RoadRunner:**

   To run the application using RoadRunner, execute:

    ```
   docker-compose up --build
    ```
   
## 📚 Documentation

- [Symfony](https://symfony.com/doc/current/index.html)
- [RoadRunner](https://roadrunner.dev/docs)

## 🤝 Contributing

If you want to contribute to the project, please create a pull request or open an issue. We always welcome new ideas and suggestions.

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

Built with ❤️ using Symfony and RoadRunner.
