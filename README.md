# Swarrot Bridge for Symfony Message component

This bridge allows you to use the great designed [Swarrot library](https://github.com/swarrot/swarrot) to consume and produce messages to various message brokers.

## Usage

1. Install Swarrot's bridge
```
composer req sroze/swarrot-bridge:dev-master
```

2. Configure Swarrot Bundle within your application. 
```yaml
# config/packages/swarrot.yaml
swarrot:
    default_connection: rabbitmq
    connections:
        rabbitmq:
            host: 'localhost'
            port: 5672
            login: 'guest'
            password: 'guest'
            vhost: '/'

    consumers:
        my_consumer:
            processor: app.message_processor
            middleware_stack:
                 - configurator: swarrot.processor.signal_handler
                 - configurator: swarrot.processor.max_messages
                   extras:
                       max_messages: 100
                 - configurator: swarrot.processor.doctrine_connection
                   extras:
                       doctrine_ping: true
                 - configurator: swarrot.processor.doctrine_object_manager
                 - configurator: swarrot.processor.exception_catcher
                 - configurator: swarrot.processor.ack

    messages_types:
        my_publisher:
            connection: rabbitmq # use the default connection by default
            exchange: my_exchange
```

**Important note:** Swarrot will not automatically create the exchanges, queues and bindings for you. You need to manually
configure these within RabbitMq (or another connector you use).

3. Register producer and processor.
```yaml
# config/services.yaml
services:
    # ...
    
    app.message_producer:
        class: Sam\Symfony\Bridge\SwarrotMessage\SwarrotProducer
        arguments:
        - "@swarrot.publisher"
        - "@message.transport.default_encoder"
        - my_publisher

    app.message_processor:
        class: Sam\Symfony\Bridge\SwarrotMessage\SwarrotProcessor
        arguments:
        - "@message_bus"
        - "@message.transport.default_decoder"
```

See that the processor is something Swarrot-specific. As Swarrot's power is to consume messages, we won't use the Message
component's command in this context but Swarrot's command. We've configured Swarrot to use this processor in the previous file's configuration.

4. Route your messages to the bus
```yaml
# config/packages/framework.yaml
    message:
        routing:
            'App\Message\MyMessage': app.message_producer
```

5. Consume your messages!
```bash
bin/console swarrot:consume:my_consumer queue_name_you_created
```

