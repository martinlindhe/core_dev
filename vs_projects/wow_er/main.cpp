/*
WoWSniffer, captures the World of Warcraft-Chat traffic and displays it
Copyright (C) 2006  cReDiAr 

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version. 

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details. 

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
MA 02110, USA 

World of Warcraft is copyrighted by Blizzard Entertainment

!! FOR EDUCATIONAL PURPOSE ONLY  !!

Be aware capturing packets of WoW is against the Policy.
http://www.blizzard.com/support/wowgm/?id=agm01716p#dsmdm
*/

#include <winsock2.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include <mstcpip.h>
#include <iphlpapi.h>


#pragma comment(lib,"ws2_32")
#pragma comment(lib,"iphlpapi")


//works till 1.10.2
unsigned char  WoW183IdentA[] = { 0x00,0x00,0x00,0x00,0x00 };
unsigned char  WoW183IdentB[] = { 0x00,0x00,0x00};


HANDLE		hCmd[1];					// Handles auf die Threads
DWORD			dwCmdID[1];				// IDs der Threads


DWORD WINAPI CmdFunc(LPVOID data)
{
	char Cmdbuf[10];
	
	while(1)
	{
		printf(">");
		scanf_s("%9s", Cmdbuf, 10);

		if( strstr(Cmdbuf,"exit") != NULL || strstr(Cmdbuf,"close") != NULL || strstr(Cmdbuf,"quit") != NULL ) {
			printf("quit....\n");
			CloseHandle(hCmd[0]); 
			exit(0);
		} else {
			printf("Error: unknown cmd\n");
		}
	}

	return 0;
}

int main()
{
	DWORD		dwSize = 0,
					dwErr,
					dwBufferLen[10],
					dwBufferInLen = 1,
					dwBytesReturned = 0 ;

	long		Ip = 0,
					LocalIp = 0;

	char		IpSrc[14],
					IpDst[14],
					tIpBuf[14],
					szErr[50],
					tbuf[20],
					buf[1000],
					bufwork[1000];	

	int			PortSrc = 0,
					PortDst = 0,
					iRet,
					nRet,
					UsedDeviceID = 0,
					i=0,
					WoWVersion = 0,
					SrcPort = 0;

	SOCKADDR_IN		sa;

	SOCKET				m_s;
	WORD					wVersionRequested;

	WSADATA			wsaData;
	
	printf("\tWoWSniffer 0.1a\n");
	printf("\n");
	printf("\t     by\n\n");
	printf("\t   cReDiAr\n\n");

	wVersionRequested	=	MAKEWORD(1,1);

	// Here read all IPs of this host
	GetIpAddrTable( NULL , &dwSize, FALSE ) ;
	PMIB_IPADDRTABLE pIpAddrTable = (PMIB_IPADDRTABLE )new BYTE [ dwSize ]; 

	tbuf[0] = -1;

	if( pIpAddrTable )
	{
		if( GetIpAddrTable( (PMIB_IPADDRTABLE)pIpAddrTable, &dwSize, FALSE ) == NO_ERROR )
		{
 			if(  pIpAddrTable->dwNumEntries > 2 ) // Second is MS TCP loopback IP ( 127.0.0.1 )
			{
				printf("Please select IP on device:\n");
				char szIP[16];

				for( int i = 0 ; i < (int)pIpAddrTable->dwNumEntries-1 ; i++ )
				{
					in_addr ina;
					ina.S_un.S_addr = pIpAddrTable->table[i].dwAddr;
 					char *pIP = inet_ntoa( ina );
					strcpy_s( szIP , pIP );
					if( _stricmp( szIP , "127.0.0.1" ) )
					{
						Ip = (long)pIpAddrTable->table[i].dwAddr;
						sprintf_s(tIpBuf, "%d.%d.%d.%d", Ip&0xFF,(Ip>>8)&0xFF,(Ip>>16)&0xFF,(Ip>>24)&0xFF);
						printf("[%d]: %s\n",i+1,tIpBuf);
					}
				}
			} else if ( pIpAddrTable->dwNumEntries == 2 ) {
				printf("Only one device detected...\n");
				tbuf[0] = '1';
				tbuf[1] = '\0';
			}
		}
	}

	//Wait for user to select which IP to sniff
	while(1)
	{
		if(tbuf[0] == -1)
		{
			printf("\n>");
			scanf_s("%s", tbuf, 2);
		}
		if( atoi(tbuf) <= (int)pIpAddrTable->dwNumEntries && tbuf[0] != -1 && atoi(tbuf) != 0 )
		{
			//printf("\n%d",atoi(tbuf));
			UsedDeviceID = atoi(tbuf)-1;
			Ip = (long)pIpAddrTable->table[UsedDeviceID].dwAddr;
			sprintf_s(tIpBuf, "%d.%d.%d.%d", Ip&0xFF,(Ip>>8)&0xFF,(Ip>>16)&0xFF,(Ip>>24)&0xFF);
			printf("Sniffing on: %s\n", tIpBuf);
			LocalIp = Ip;
			break;
		} else {
			printf("Invalid device :%d\n", atoi(tbuf));
			tbuf[0] = -1;
		}
		Sleep(1);
	}

	// start up WSA
	nRet = WSAStartup(wVersionRequested, &wsaData);
	if (wsaData.wVersion != wVersionRequested)
	{	
		fprintf(stderr,"\n Wrong version\n");
		return 0;
	}

	//create raw(!) socket
 	m_s = socket( AF_INET , SOCK_RAW , IPPROTO_IP );
	if( INVALID_SOCKET == m_s )
	{
		dwErr = WSAGetLastError() ;
		printf("Error socket() = %ld \n" , dwErr );
		closesocket( m_s ) ;
		return 0;
	}

	int rcvtimeo = 5000; // 5 sec insteadof 45 as default
	if( setsockopt( m_s , SOL_SOCKET , SO_RCVTIMEO , (const char *)&rcvtimeo , sizeof(rcvtimeo) ) == SOCKET_ERROR)
	{
		dwErr = WSAGetLastError();
		printf("Error WSAIoctl = %ld \n" , dwErr );
		closesocket( m_s );
		return 0;
	}

	sa.sin_family = AF_INET;
 	sa.sin_port = htons(7000);
	sa.sin_addr.s_addr = pIpAddrTable->table[UsedDeviceID].dwAddr;
    if (bind(m_s,(PSOCKADDR)&sa, sizeof(sa)) == SOCKET_ERROR)
	{
		dwErr = WSAGetLastError();
		printf("Error bind() = %ld\n" , dwErr );

		closesocket( m_s );
		return 0;
	}

	if( SOCKET_ERROR == WSAIoctl( m_s, SIO_RCVALL , &dwBufferInLen, sizeof(dwBufferInLen),             
																&dwBufferLen, sizeof(dwBufferLen),
																&dwBytesReturned , NULL , NULL ) )
	{
		dwErr = WSAGetLastError();
		printf("Error WSAIoctl = %ld\n" , dwErr );
		closesocket( m_s );
		return 0;
	}

	printf("listening on %s\n",tIpBuf);

	//create new thread
	hCmd[0] = CreateThread(NULL,0,CmdFunc,0,0,&dwCmdID[0]);

	//the default realm server is running on this port, so filter for it to avoid non wow-traffic
	SrcPort = 3724;

	while(1)
	{
		Sleep(1);
		iRet = 	recv( m_s , buf , sizeof( buf ) , 0 );
		if( iRet == SOCKET_ERROR )
		{
			dwErr = WSAGetLastError();
			sprintf_s( szErr , "Error recv() = %ld " , dwErr );
			continue;
		} else {
			if( *buf )
			{
				if( iRet > 40 )
				{
					memcpy(bufwork,buf,sizeof(buf));
					bufwork[iRet] = '\0';
					memcpy(tbuf,bufwork+0x1e,4);

					sprintf_s(IpSrc,"%.d.%.d.%.d.%.d",buf[12]&0xff,buf[13]&0xff,buf[14]&0xff,buf[15]&0xff);
					sprintf_s(IpDst,"%.d.%.d.%.d.%.d",buf[16]&0xff,buf[17]&0xff,buf[18]&0xff,buf[19]&0xff);
					//sprintf(tbuf,"%d%d",buf[20]&0xff,);
					PortSrc = ((buf[20]&0xff)<<8)+(buf[21]&0xff);
					PortDst = ((buf[22]&0xff)<<8)+(buf[23]&0xff);

					//see above
					if ( SrcPort != PortSrc && SrcPort != 0 )
						continue;
/*
	WoW(Chat)
		since 1.6.1
		we're looking for these bytes:
		x is always 0
		y is the text length and is always different
								xxxxxxxxxxYYxxxxxx
		E0531562030700000043912700000000002D00000073616368746D616C206B616E6E20766F6E20756E732065696E6572202B313520626577207A61756265726E3F0000
		2FA39F9E0307000000B8C402000000000023000000697374204F6E7920656967656E746C69636820697267656E6477616E6E20746F743F0000
		4A58F8920307000000E97B1700000000005A0000006B616E6E20776572203135205374C3A4726B652061756620646173206D616368656E3F207C6366663030373064647C486974656D3A31383638333A303A303A307C685B48616D6D657220646572205665737065725D7C687C720000
*/
					WoWVersion = 0;

					for(i=0x0;i!=iRet;++i)
					{
						if (i > 1000-(sizeof(WoW183IdentA)+1+sizeof(WoW183IdentB)))
							break;
									
						if(  memcmp(buf+i,WoW183IdentA,sizeof(WoW183IdentA)) == 0 )
						{
							i+=1+sizeof(WoW183IdentA);
							if(  memcmp(buf+i,WoW183IdentB,sizeof(WoW183IdentB)) == 0 )
							{
								if ( buf[i+sizeof(WoW183IdentB)] >= 0x20 && buf[i+sizeof(WoW183IdentB)] <= 0x7E )
								{
									WoWVersion = 2;
									memcpy(bufwork,buf+i+sizeof(WoW183IdentB),sizeof(buf)-(i+sizeof(WoW183IdentB)));	
								}
								break;
							}
						}
					}

					if ( i < 1000 && i != iRet && WoWVersion != 0 )
					{
						i = (buf[(i+sizeof(WoW183IdentB))-4]&0xFF);	//msg length
						if(i!=0)
						{
							printf("[%s:%d][%s:%d]:\n",IpSrc,PortSrc,IpDst,PortDst);
							printf("[WoW-Chat(0x%02X)]:%s\n",i,bufwork);
						}
					}

					memset(buf,0x00,sizeof(buf));
					memset(bufwork,0x00,sizeof(bufwork));
				}
			} else {
				;//printf("\rno data");				
			}
		}
	}

	return 0;
}