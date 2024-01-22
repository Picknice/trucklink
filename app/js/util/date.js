function time()
{
    return new Date().getTime();
}
function timestamp()
{
    return Math.round( time() / 1000 );
}
function ending( val, fn )
{
    let arr = Array.isArray( fn ) ? fn : [];
    fn = typeof( fn ) == 'function' ? fn : function( val ){
        let value = parseInt( val );
        if( !isNaN( value ) && value === val ){
            if( value > 10 && value < 20 )
                return arr[2] !== undefined ? arr[2] : '';
            if( value % 10 === 1 )
                return arr[0] !== undefined ? arr[0] : '';
            else if( value % 10 > 1 && value % 10 <= 4 )
                return arr[1] !== undefined ? arr[1] : '';
            else
                return arr[2] !== undefined ? arr[2] : '';
        }
        return val;
    };
    return fn.call( this, val );
}
function date( format, tm )
{
    tm = tm === undefined ? timestamp() : tm;
    let date = new Date( tm * 1000 );
    let Y = date.getFullYear().toString();
    let y = Y.substring(2);
    let d = ("0"+date.getDate()).slice(-2);
    let m = ("0"+(date.getMonth()+1)).slice(-2);
    let H = ("0"+date.getHours()).slice(-2);
    let h = ("0"+(date.getHours()>11?date.getHours()-12:date.getHours())).slice(-2);
    let i = ("0"+date.getMinutes()).slice(-2);
    let s = ("0"+date.getSeconds()).slice(-2);
    return format.replace('d',d).replace('m',m).replace('y',y).replace('Y',Y).replace('H',H).replace('h',h).replace('i',i).replace('s',s);
}
function endingPref( val, arr, pref, addVal )
{
    arr = Array.isArray( arr ) ? arr : [];
    if( typeof( pref ) == 'boolean' ){
        addVal = pref;
        pref = '';
    }
    addVal = typeof( addVal ) == 'boolean' ? addVal : true;
    pref = typeof( pref ) == 'string' ? pref : '';
    return (addVal&&val!==1?val+' ':'') + pref + (pref!==''?' ':'') + ending( val, arr );
}
function endingDays( val, pref, addVal )
{
    return endingPref( val, [ 'день', 'дня', 'дней' ], pref, addVal )
}
function endingHours( val, pref, addVal )
{
    return endingPref( val, [ 'час', 'часа', 'часов' ], pref, addVal );
}
function endingMinutes( val, pref, addVal )
{
    return endingPref( val, [ 'минуту', 'минуты', 'минут' ], pref, addVal );
}
function endingTime( val )
{
    return ending( val, function(){
        let tm = parseInt( val );
        if( !isNaN( tm ) && tm === val ){
            let days = Math.floor( tm / 86400 );
            let tm = tm - days * 86400;
            if( days > 0 )
                return endingDays( days );
            else{
                let h = Math.floor( tm / 3600 );
                tm = tm - h * 3600;
                if( h > 0 ){
                    return endingHours( h );
                }else{
                    let m = Math.floor( tm / 60 );
                    if( m > 0 ){
                        return endingMinutes( m );
                    }else
                        return val;
                }
            }
        }
        return val;
    } );
}