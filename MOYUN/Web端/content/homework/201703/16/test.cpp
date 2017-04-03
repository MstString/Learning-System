#include<stdio.h>
#include<string.h>
#include<math.h>
int map[3][4];
int flag[15];
int map_vis[3][4];   //标记数组
int s_flag[15];    //标记数组
int s[15];       //存所选的数
int dir[4][2]={{1,0},{0,1},{-1,0},{0,-1}};
int sum=0,index=0;
void dfs1(int x,int y)
{                         //判断5个数是否相连
    int sx,sy;
       for(int i=0;i<4;i++)
       {
         sx=x+dir[i][0];
         sy=y+dir[i][1];
         if(sx<0 || sx>=3 || sy<0 || sy>=4)
            continue;                         //出界判断
         if(!s_flag[map[sx][sy]]|| map_vis[sx][sy])
           continue;
             map_vis[sx][sy]=1;
             index++;
             dfs1(sx,sy);
       }
}
void check()
{
    int x,y;
    memset(map_vis,0,sizeof(map_vis));
    memset(s_flag,0,sizeof(s_flag));
    int i;
    for(i=0;i<6;i++)
        s_flag[s[i]]=1;      
    for(i=0;i<12;i++)
    {
        x=i/4;
        y=i%4;
        if(s_flag[map[x][y]])    //找到所选的数在map数组中的位置
        {
            index=1;
            map_vis[x][y]=1;
            dfs1(x,y);
            break;
        }
    }
    if(index==5)
        sum++;
}
void dfs(int count)
{                         //列出从12个数中不重复选5个数的所有组合
    int i;
    if(count==6)
    {
          check();
        return;
    }
    else
    {
        for(i=s[count-1]+1;i<=12;i++)
        {
            if(!flag[i])
            {
                flag[i]=1;
                s[count]=i;     //将选好的5个数放到s数组中 
                dfs(count+1);
                flag[i]=0;
            }
        }
    }
}
int main()
{
    int i,j,k=1;
    for(i=0;i<3;i++)
        for (j=0;j<4;j++)
        map[i][j]=k++;
        dfs(1);
        printf("%d\n",sum);
    return 0;
}


