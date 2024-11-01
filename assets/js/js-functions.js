function shopctTwoDigits(d) {
    if(0 <= d && d < 10) return "0" + d.toString();
    if(-10 < d && d < 0) return "-0" + (-1*d).toString();
    return d.toString();
}

Date.prototype.toMysqlFormat = function() {
    return this.getFullYear() + "-" + shopctTwoDigits(1 + this.getMonth()) + "-" + shopctTwoDigits(this.getDate()) + " " + shopctTwoDigits(this.getHours()) + ":" + shopctTwoDigits(this.getMinutes()) + ":" + shopctTwoDigits(this.getSeconds());
};

/* check if object is empty */
var shopctIsEmpty = function(obj){
	for(var key in obj) {
        if(obj.hasOwnProperty(key)){
            return false;
        }
    }
    return true;
};
