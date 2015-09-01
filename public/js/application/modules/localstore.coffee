MusicLoud = angular.module("MusicLoud")

MusicLoud.config ["$indexedDBProvider", ($indexedDBProvider) ->

  $indexedDBProvider
    .connection("MusicLoud")

    .upgradeDatabase 1, (event, db, tx) ->
      trackStorage = db.createObjectStore('tracks', keyPath: 'id')
      trackStorage.createIndex('track_title_idx',   'track_title',  unique: false)
      trackStorage.createIndex('track_artist_idx',  'track_artist', unique: false)
      trackStorage.createIndex('track_album_idx',   'track_album',  unique: false)
      trackStorage.createIndex('track_genre_idx',   'track_genre',  unique: false)
      trackStorage.createIndex('track_year_idx',    'track_year',   unique: false)
      trackStorage.createIndex('track_rating_idx',  'track_rating', unique: false)
      trackStorage.createIndex('is_favourite_idx',  'is_favourite', unique: false)
      trackStorage.createIndex('album_artist_idx',  'album_artist', unique: false)

    .upgradeDatabase 2, (event, db, tx) ->
      infoStorage = db.createObjectStore('misc', keyPath: 'key')

]

MusicLoud.run ["$indexedDB", "DBSyncService", ($indexedDB, DBSyncService) ->
  $indexedDB.openStore 'misc', (store) ->
    store.find('sync_token').then((data) -> data.value).catch(-> 0).then (token) ->
      DBSyncService.loadAll().success((content) ->
        console.log(content)
      )
      store.upsert(key: 'sync_token', value: token)
]

MusicLoud.service("DBSyncService", ["$http", ($http) ->
  loadAll: -> $http.get("/api/resources/tracks/all")
])