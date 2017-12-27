import { md5 } from 'vux'
import queryStringify from './query-stringify'
const __KEY__ = 'c8428d0773fc7a656b7cfcfb0cb4ac62' //sign验证密钥
/**
 * JSON对象按照ASCII码排序的函数
 * @param {Object} obj   需要排序的JSON对象
 */
export const objSort = function (obj) {
  let newkey = Object.keys(obj).sort();
  //先用Object内置类的keys方法获取要排序对象的属性名，再利用Array原型上的sort方法对获取的属性名进行排序，newkey是一个数组
  let newObj = {};//创建一个新的对象，用于存放排好序的键值对
  for (let i = 0; i < newkey.length; i++) {//遍历newkey数组
    let temp = obj[newkey[i]]
    if (temp || (!isNaN(temp) && temp !== ''))
      newObj[newkey[i]] = temp;//向新创建的对象中按照排好的顺序依次增加键值对
  }
  return newObj;//返回排好序的新对象
}

/**
 * 生成随机校验串
 * @param {Int} len
 */
export const randomString = function (len = 32) {
  /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
  let $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
  let maxPos = $chars.length;
  let pwd = '';
  for (let i = 0; i < len; i++) {
    pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
  }
  return pwd;
}

/**
 * 签名验证
 * @param {Object} params
 */
export default function(params) {
  let str = queryStringify(objSort(params)) + '&key=' + __KEY__;
  let sign_temp = md5(str).toUpperCase(), sign = [];
  for (let i = 0; i < sign_temp.length; i++) {
    if (i % 2 === 0)
      sign.push(sign_temp.substr(i, 2))
  }
  sign.forEach((v, i) => sign[i] += randomString(1))
  return sign.join('');
}
