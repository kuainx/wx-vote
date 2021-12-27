import { wx_init } from './wx';
import { init } from './init';
import { createApp } from 'vue';
import { Button, Card, Input } from 'ant-design-vue';
import axios from 'axios';
import App from './App.vue';
// import 'ant-design-vue/dist/antd.css';

init();
wx_init();
axios.defaults.baseURL = 'https://lifestudio.cn/vote/api/api.php';
const app = createApp(App);
app.use(Button);
app.use(Card);
app.use(Input);
app.mount('#app');
