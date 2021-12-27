<script setup>
import { UserOutlined, InfoCircleOutlined } from '@ant-design/icons-vue'
import { ref } from 'vue'
import { getVote, getUser } from './content'
import axios from 'axios';
import { wx_share } from '../wx';

const title = ref("加载中...")
const user = ref("用户")
const nick = ref(null)
const value = ref(0)
const option = ref([])
const disable = ref(true)
const link = ref()
const num = ref(0)

const submit = async () => {
  const r = await axios.post('?t=vote', 'optionid=' + value.value + '&openid=' + window.user_data.user + '&nickname=' + nick.value);
  const r1 = r.data;
  if (r1.status !== 0) {
    alert(r1.ret);
  } else {
    alert("投票成功")
  }
}
const init = async () => {
  const voteData = await getVote()
  const voteId = voteData[0][0].id
  title.value = voteData[0][0].content
  option.value = voteData[1]
  for (const item of option.value) {
    num.value += parseInt(item.num)
  }
  const userData = await getUser()
  user.value = userData.username
  nick.value = userData.nickname
  const userVote = JSON.parse(userData.vote)
  value.value = parseInt(userVote[voteId] || 0).toString()
  if (value.value == "0") {
    disable.value = false
  } else {
    alert("您已投票")
  }
  link.value = "https://lifestudio.cn/vote/?vote=" + window.user_data.vote
  wx_share("快来参与" + title.value + "投票", link.value, "https://t7.baidu.com/it/u=2235388275,3809603206&fm=85&app=79&f=JPG?w=121&h=75&s=8197C732C535FA313E526557030030BB", "已有" + num.value + "人参与")

}
init()
const goBack = () => {
  if (window.user_data.currentVote > 0) {
    window.user_data.currentVote--;
    init()
  } else {
    alert('已是第一个');
  }
};

const goForward = () => {
  if (window.user_data.currentVote < window.user_data.voteList.length - 1) {
    window.user_data.currentVote++;
    init()
  } else {
    alert('已是最后一个');
  }
};

</script>

<template>
  <div style="height:100px"></div>
  <h2 style="color:#f1f1f1">欢迎 {{ user }}，来投票</h2>
  <div style="width:300px;margin:auto">
    <div style="margin:5px">
      <a-input v-model:value="nick" placeholder="输入姓名">
        <template #prefix>
          <user-outlined type="user" />
        </template>
        <template #suffix>
          <a-tooltip title="输入姓名">
            <info-circle-outlined style="color: rgba(0, 0, 0, 0.45)" />
          </a-tooltip>
        </template>
      </a-input>
    </div>
  </div>
  <div class="card">
    <a-card :title="title" style="width: 300px;margin:auto">
      <h3>已有{{ num }}人参与</h3>
      <div style="margin:5px">
        <a-radio-group v-model:value="value">
          <a-radio class="ratio" v-for="item in option" :value="item.id">{{ item.content }}</a-radio>
        </a-radio-group>
      </div>

      <div style="margin:5px">
        <a-button-group>
          <a-button type="primary" @click="goBack">
            <a-icon type="left" />上一个
          </a-button>
          <a-button :disabled="disable" type="primary" @click="submit">提交</a-button>
          <a-button type="primary" @click="goForward">
            下一个
            <a-icon type="right" />
          </a-button>
        </a-button-group>
      </div>
      <div style="margin:5px">
        <a-input v-model:value="link" class="share-link" />
      </div>
    </a-card>
  </div>
</template>

<style scoped>
.card {
  text-align: center;
}
.ratio {
  display: flex;
  height: 30px;
  line-height: 30px;
}
</style>
