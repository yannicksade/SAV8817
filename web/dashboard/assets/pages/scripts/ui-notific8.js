var Alert = (function () {
    var settings = {
        sticky: false,
        horizontalEdge: 'top',
        verticalEdge: 'right',
        life: 10000
    };
    var SystemNotificationSettings = {
        ele: 'body', // which element to append to
        offset: {from: 'top', amount: 52},
        align: 'center', // 'top', or 'bottom'
        width: 'auto', // (integer, or 'auto')
        delay: 20000, // Time while the message will be displayed. It's not equivalent to the *demo* timeOut!
        allow_dismiss: true, // If true then will display a cross to close the popup.
        stackup_spacing: 5 // spacing between consecutively stacked growls.

    };
    return {

        //main function to initiate the module
        init: function () {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'http://localhost/SAV8817.git/web/app_dev.php/easy-trade/user/dashboard/ajax-notification');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var groupe_notifications = JSON.parse(xhr.responseText);
                    //var message;
                    var i = 0;
                    for (var type in groupe_notifications) {
                        for (var id in groupe_notifications[type]) {
                            (function (_id, _type) {
                                var message = groupe_notifications[_type][_id];
                                if (message.indexOf('_&') > -1) {
                                    setTimeout(function () {
                                        var header_message = message.split('_&');
                                        settings.heading = header_message[0];
                                        notify(_type, header_message[1]);
                                    }, 3000 * i++);
                                } else {
                                    setTimeout(function () {
                                        SystemNotification(_type, message);
                                    }, 5000 * i++);
                                }
                            })(id, type);
                        }
                    }
                }
            };
            xhr.send(null);

            function notify(type, message) {
                $.notific8('zindex', 11500); //flash-'.$type.'
                switch (type) {
                    case 'error':
                    case 'danger':
                        settings.theme = 'ruby';
                        break;
                    case 'success' :
                        settings.theme = 'lime';
                        break;
                    case 'warning':
                        settings.theme = 'tangerine';
                        break;
                    case 'info' :
                        settings.theme = 'smoke';
                        break;
                    case 'notification' :
                        settings.theme = 'lemon';
                        break;
                    default :
                        settings.theme = 'teal';
                }
                $.notific8($.trim(message), settings);

            }

            function SystemNotification(type, message) {
                SystemNotificationSettings.type = type;
                $.bootstrapGrowl(message, SystemNotificationSettings);

            }
        }
    }
})();

jQuery(document).ready(function () {
    document.body.onfocus = (function () {
        setInterval(Alert.init(), 1000);
    })();
});