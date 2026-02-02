FROM migrate/migrate:v4.19.1

COPY docker/migration/entrypoint.sh /entrypoint.sh
COPY migrations /migrations

ENTRYPOINT ["/entrypoint.sh"]
