#include <iostream>
using namespace std;
#define Z 4
#define Y 3
#define X 2
int a[4][4],n,m[4][4];
int l(int x,int y)
{
	int k=0;
	if (y-1>=0&&a[x][y-1]==n)
	{
		if (y-2>=0&&!a[x][y-2]) k+=X;
		if (y+1<4&&!a[x][y+1]) k+=X;
	}
	return k;
}
int r(int x,int y)
{
	int k=0;
	if (y+1>=0&&a[x][y+1]==n)
	{
		if (y+2<4&&!a[x][y+2]) k+=X;
		if (y-1>=0&&!a[x][y-1]) k+=X;
	}
	return k;
}
int u(int x,int y)
{
	int k=0;
	if (x-1>=0&&a[x-1][y]==n)
	{
		if (x-2>=0&&!a[x-2][y]) k+=X;
		if (x+1<4&&!a[x+1][y]) k+=X;
	}
	return k;
}
int d(int x,int y)
{
	int k=0;
	if (x+1>=0&&a[x+1][y]==n)
	{
		if (x+2<4&&!a[x+2][y]) k+=X;
		if (x-1>=0&&!a[x-1][y]) k+=X;
	}
	return k;
}
int um(int x,int y)
{
	int k=0;
	if (x-1>=0&&y-1>=0&&a[x-1][y-1]==n)
	{
		if (x-2>=0&&y-2>=0&&!a[x-2][y-2]) k+=X;
		if (x+1<4&&y+1<4&&!a[x+1][y+1]) k+=X;
	}
	return k;
}
int dm(int x,int y)
{
	int k=0;
	if (x+1<4&&y+1<4&&a[x+1][y+1]==n)
	{
		if (x+2<4&&y+2<4&&!a[x+2][y+2]) k+=X;
		if (x-1>=0&&y-1>=0&&!a[x-1][y-1]) k+=X;
	}
	return k;
}
int up(int x,int y)
{
	int k=0;
	if (x-1>=0&&y+1<4&&a[x-1][y+1]==n)
	{
		if (x-2>=0&&y+2<4&&!a[x-2][y+2]) k+=X;
		if (x+1<4&&y-1>=0&&!a[x+1][y-1]) k+=X;
	}
	return k;
}
int dp(int x,int y)
{
	int k=0;
	if (x+1<4&&y-1>=0&&a[x+1][y-1]==n)
	{
		if (x+2<4&&y-2>=0&&!a[x+2][y-2]) k+=X;
		if (x-1>=0&&y+1<4&&!a[x-1][y+1]) k+=X;
	}
	return k;
}
int lh(int x,int y)
{
	int k=0;
	if (y-2>=0)
	{
		if (a[x][y-2]==n&&a[x][y-1]==n) k+=Z+Y;
		else k+=l(x,y);
		if (a[x][y-2]==3-n&&a[x][y-1]==3-n) k+=Z;
	}
	else k+=l(x,y);
	return k;
}
int h(int x,int y)
{
	int k=0;
	if (y-1>=0&&y+1<4)
	{
		if (a[x][y-1]==n&&a[x][y+1]==n) k+=Z+Y;
		if (a[x][y-1]==3-n&&a[x][y+1]==3-n) k+=Z;
	}
	return k;
}
int rh(int x,int y)
{
	int k=0;
	if (y+2<4)
	{
		if (a[x][y+2]==n&&a[x][y+1]==n) k+=Z+Y;
		else k+=r(x,y);
		if (a[x][y+2]==3-n&&a[x][y+1]==3-n) k+=Z;
	}
	else k+=r(x,y);
	return k;
}
int uv(int x,int y)
{
	int k=0;
	if (x-2>=0)
	{
		if (a[x-2][y]==n&&a[x-1][y]==n) k+=Z+Y;
		else k+=u(x,y);
		if (a[x-2][y]==3-n&&a[x-1][y]==3-n) k+=Z;
	}
	else k+=u(x,y);
	return k;
}
int v(int x,int y)
{
	int k=0;
	if (x-1>=0&&x+1<4)
	{
		if (a[x-1][y]==n&&a[x+1][y]==n) k+=Z+Y;
		if (a[x-1][y]==3-n&&a[x+1][y]==3-n) k+=Z;
	}
	return k;
}
int dv(int x,int y)
{
	int k=0;
	if (x+2<4)
	{
		if (a[x+2][y]==n&&a[x+1][y]==n) k+=Z+Y;
		else k+=d(x,y);
		if (a[x+2][y]==3-n&&a[x+1][y]==3-n) k+=Z;
	}
	else k+=d(x,y);
	return k;
}
int udm(int x,int y)
{
	int k=0;
	if (x-2>=0&&y-2>=0)
	{
		if (a[x-2][y-2]==n&&a[x-1][y-1]==n) k+=Z+Y;
		else k+=um(x,y);
		if (a[x-2][y-2]==3-n&&a[x-1][y-1]==3-n) k+=Z;
	}
	else k+=um(x,y);
	return k;
}
int dmm(int x,int y)
{
	int k=0;
	if (x-1>=0&&y-1>=0&&x+1<4&&y+1<4)
	{
		if (a[x-1][y-1]==n&&a[x+1][y+1]==n) k+=Z+Y;
		if (a[x-1][y-1]==3-n&&a[x+1][y+1]==3-n) k+=Z;
	}
	return k;
}
int ddm(int x,int y)
{
	int k=0;
	if (x+2<4&&y+2<4)
	{
		if (a[x+2][y+2]==n&&a[x+1][y+1]==n) k+=Z+Y;
		else k+=dm(x,y);
		if (a[x+2][y+2]==3-n&&a[x+1][y+1]==3-n) k+=Z;
	}
	else k+=dm(x,y);
	return k;
}
int udp(int x,int y)
{
	int k=0;
	if (x-2>=0&&y+2<4)
	{
		if (a[x-2][y+2]==n&&a[x-1][y+1]==n) k+=Z+Y;
		else k+=up(x,y);
		if (a[x-2][y+2]==3-n&&a[x-1][y+1]==3-n) k+=Z;
	}
	else k+=up(x,y);
	return k;
}
int dpp(int x,int y)
{
	int k=0;
	if (x-1>=0&&y+1<4&&x+1<4&&y-1>=0)
	{
		if (a[x-1][y+1]==n&&a[x+1][y-1]==n) k+=Z+Y;
		if (a[x-1][y+1]==3-n&&a[x+1][y-1]==3-n) k+=Z;
	}
	return k;
}
int ddp(int x,int y)
{
	int k=0;
	if (x+2<4&&y-2>=0)
	{
		if (a[x+2][y-2]==n&&a[x+1][y-1]==n) k+=Z+Y;
		else k+=dp(x,y);
		if (a[x+2][y-2]==3-n&&a[x+1][y-1]==3-n) k+=Z;
	}
	else k+=dp(x,y);
	return k;
}
void clear()
{
	for (int i=0;i<4;i++)
		for (int j=0;j<4;j++) m[i][j]=1;
	m[0][0]=m[0][3]=m[3][0]=m[3][3]=0;
	for (int i=1;i<3;i++)
		for (int j=1;j<3;j++) m[i][j]+=2;
}
int main()
{
	clear();	
	cin >> n;
	char c;
	for (int i=0;i<4;i++)
		for (int j=0;j<4;j++)
		{
			cin >> c;
			a[i][j]=int(c-'0');
		}
	for (int i=0;i<4;i++)
		for (int j=0;j<4;j++)
			if (!a[i][j]) m[i][j]+=rh(i,j)+h(i,j)+lh(i,j)+uv(i,j)+v(i,j)+dv(i,j)+udm(i,j)+dmm(i,j)+ddm(i,j)+udp(i,j)+dpp(i,j)+ddp(i,j);
	int x,y,mx=-1;
	for (int i=0;i<4;i++)
		for (int j=0;j<4;j++)
			if (!a[i][j]&&m[i][j]>=mx)
			{
				mx=m[i][j];
				x=i;
				y=j;
			}
	cout << x+1 << ' ' << y+1 << endl;
	return 0;
}