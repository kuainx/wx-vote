import axios from 'axios';

export const getVote = async () => {
  const r = await axios.post('?t=getvote', 'voteid=' + window.user_data.vote);
  const r1 = r.data;
  if (r1.status !== 0) {
    alert(r1.ret);
  } else {
    return r1.ret;
  }
};
export const getUser = async () => {
  const r = await axios.post('?t=getuser', 'openid=' + window.user_data.user);
  const r1 = r.data;
  if (r1.status !== 0) {
    alert(r1.ret);
  } else {
    return r1.ret[0];
  }
};
