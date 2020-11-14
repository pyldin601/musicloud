FROM migrate/migrate:v4.13.0

COPY docker/migration/entrypoint.sh /entrypoint.sh
COPY migrations /migrations

ENTRYPOINT ["/entrypoint.sh"]
