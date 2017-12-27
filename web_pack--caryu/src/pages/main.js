import Vue from 'vue'
import { dateFormat } from '@/utils'
import { ajaxPlugin } from '@/plugins'

/* Element-UI The Start */
import {
  Notification,
  Message,
  Loading,
  Row,
  Col,
  Card,
  Button,
  DatePicker,
  Tooltip,
  Menu,
  Submenu,
  MenuItem,
  // MenuItemGroup,
  Dropdown,
  DropdownMenu,
  DropdownItem,
  Collapse,
  CollapseItem,
  Badge,
  Tabs,
  TabPane,
  Form,
  FormItem,
  Input,
  InputNumber,
  Select,
  CheckboxGroup,
  Checkbox,
  CheckboxButton,
  Option,
  Dialog,
  Pagination,
  Table,
  TableColumn,
} from 'element-ui'

Vue.use(Loading)
Vue.use(Row)
Vue.use(Col)
Vue.use(Card)
Vue.use(Button)
Vue.use(DatePicker)
Vue.use(Tooltip)
Vue.use(Menu)
Vue.use(Submenu)
Vue.use(MenuItem)
// Vue.use(MenuItemGroup)
Vue.use(Dropdown)
Vue.use(DropdownMenu)
Vue.use(DropdownItem)
Vue.use(Collapse)
Vue.use(CollapseItem)
Vue.use(Badge)
Vue.use(Tabs)
Vue.use(TabPane)
Vue.use(Form)
Vue.use(FormItem)
Vue.use(Input)
Vue.use(InputNumber)
Vue.use(Select)
Vue.use(CheckboxGroup)
Vue.use(Checkbox)
Vue.use(CheckboxButton)
Vue.use(Option)
Vue.use(Dialog)
Vue.use(Pagination)
Vue.use(Table)
Vue.use(TableColumn)
/* Element-UI The End */

Vue.use(ajaxPlugin)

Vue.prototype.Notify = Notification
Vue.prototype.Msg = Message
Vue.prototype.f_addTab = window.f_addTab || window.parent.f_addTab // 新开子页面(iframe)
Vue.prototype.dateFormat = dateFormat // 格式化日期
export default Vue