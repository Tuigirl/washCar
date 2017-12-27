/**
 * 字符串指定位置进行脱敏
 * @param {String} str         要进行处理的字符串
 * @param {String} replaceStr  替换成该字符串
 * @param {Array}  pos         起始位置[start, end]
 */
export default function (str, replaceStr, pos) {
  if (str === '') return str
  return str.substr(0, pos[0] - 1) + replaceStr + str.substr(pos[1])
}