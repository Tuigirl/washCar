var fs = require('fs');
var pathName = './console.txt';

function consoleTxt(text) {//写文件
	fs.writeFile(pathName, JSON.stringify(text),function(err){
		if(err) throw err;
		console.log('into the text success');
	})
}

function consoleTxtAdd(text) {//累加写文件
	fs.readFile(pathName, function(err,data){
		if(err) throw err;
		consoleTxt(data.toString() + '\n' + text);
	})
}

module.exports = {
    consoleTxt: consoleTxt,
    consoleTxtAdd: consoleTxtAdd,
}
