/**
 * Created by Roman on 27.07.2015.
 */

var MusicLoud = angular.module("MusicLoud");

MusicLoud.run(["$rootScope", function ($rootScope) {

    $rootScope.selectedTracksMenu = function (selection) {
        var defaultMenu = [
            {
                type: 'item',
                text: '<i class="fa fa-pencil-square item-icon"></i> Edit metadata',
                action: function () {
                    $rootScope.action.editSongs(selection);
                }
            },
            {
                type: 'item',
                text: '<i class="fa fa-minus-square item-icon"></i> Delete track(s) completely',
                action: function () {
                    $rootScope.action.deleteSongs(selection);
                }
            }
        ];

        if (selection.length > 0) {

            if (selection[0].link_id) {
                defaultMenu.push({
                    type: 'item',
                    text: '<i class="fa fa-minus-square item-icon"></i> Delete track(s) from playlist',
                    action: function () {
                        $rootScope.playlistMethods.removeTracksFromPlaylist(selection)
                    }
                });
            }

        }

        switch (selection.length) {

            case 0:
                return null;

            case 1:
                defaultMenu.push({ type: 'divider' });
                if (selection[0].album_artist) {
                    defaultMenu.push({
                        type: 'item',
                        text: '<i class="fa fa-search item-icon"></i> Show all by <b>' + htmlToText(selection[0].album_artist) + '</b>',
                        href: selection[0]["artist_url"]
                    });
                }
                if (selection[0].track_album) {
                    defaultMenu.push({
                        type: 'item',
                        text: '<i class="fa fa-search item-icon"></i> Show all from <b>' + htmlToText(selection[0].track_album) + '</b>',
                        href: selection[0]["album_url"]
                    });
                }
                if (selection[0].track_genre) {
                    defaultMenu.push({
                        type: 'item',
                        text: '<i class="fa fa-search item-icon"></i> Show all by genre <b>' + htmlToText(selection[0].track_genre) + '</b>',
                        href: selection[0]["genre_url"]
                    });
                }
                break;

            default:
                break;

        }

        defaultMenu.push({ type: 'divider' });

        defaultMenu.push({
            type: 'sub',
            text: '<i class="fa fa-plus item-icon"></i> Add to playlist',
            data: $rootScope.playlists.map(function (playlist) {
                return {
                    type: 'item',
                    text: '<i class="fa fa-list item-icon"></i> ' + htmlToText(playlist.name),
                    action: function () {
                        $rootScope.playlistMethods.addTracksToPlaylist(playlist, selection);
                    }
                }
            })
        });

        return defaultMenu;

    };

}]);

