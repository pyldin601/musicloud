FROM migrate/migrate:v4.15.2

COPY docker/migration/entrypoint.sh /entrypoint.sh
COPY migrations /migrations

ENTRYPOINT ["/entrypoint.sh"]
