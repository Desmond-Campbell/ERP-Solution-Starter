var Modules = Modules || [];

function ___t( text ) {

	return text;

}

function alertError( error ) {

    app.alert('error', ___t('Error'), error, 7);

}

function alertSuccess( message ) {

    app.alert('success', ___t('Success'), message, 7);

}

function alertInfo( message ) {

    app.alert('info', ___t('Information'), message, 10);

}

function ___c( text ) {

	let message = {
        title: ___t( 'Please Confirm'),
        body: ___t( text )
    }

    return message;

}

var general_error = ___t( 'We could not submit your request due to a general error. Please refresh and try again. If you are still unsuccessful, kindly contact Support.' );

var general_error_failure = ___t( 'We are currently experience a system problem. Please refresh and try again. If you are still unsuccessful, kindly contact Support.' );

Array.prototype.move = function (old_index, new_index) {
    if (new_index >= this.length) {
        var k = new_index - this.length;
        while ((k--) + 1) {
            this.push(undefined);
        }
    }
    this.splice(new_index, 0, this.splice(old_index, 1)[0]);
    return this; // for testing purposes
};

function include( M ) {
 
    for ( var m = 0; m < M.length; m++ ) {

        Modules.push( M[m] );

    }

}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};