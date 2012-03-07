function addPanel(elemid){
    if (!elemid || typeof uLogin == 'unedfined') return;
    var element = document.getElementById(elemid);
    if (!element) return;
    var params = element.getAttribute('x-ulogin-params');
    if (params){
        var query = uLogin.parse(params);
        var found = false;
        var i;
        for (i = 0; i < uLogin.ids.length; i++)
            if (elemid == uLogin.ids[i].id) {
             found = true;
             break;
            }
            if (!found) i = uLogin.ids.length;
            uLogin.ids[i] = {
                    id:elemid,
                    dropTimer: false,
                    done: false,
                    providers : uLogin.def(query, 'providers', ''),
                    hidden : uLogin.def(query, 'hidden', ''),
                    redirect_uri : uLogin.def(query, 'redirect_uri', ''),
                    callback : uLogin.def(query, 'callback', ''),
                    fields : uLogin.def(query, 'fields', 'first_name,last_name'),
                    optional : uLogin.def(query, 'optional', ''),
                    color : uLogin.def(query, 'color', 'fff'),
                    opacity : uLogin.def(query, 'opacity', '75')
                };
		switch (query['display']){
                    case 'small':uLogin.initSmall(i);break;
                    case 'panel':uLogin.initPanel(i);break;
                    case 'window':uLogin.initWindow(i);break;
                }
    }
}
