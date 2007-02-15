# Microsoft Developer Studio Generated NMAKE File, Based on MQ2Krust.dsp
!IF "$(CFG)" == ""
CFG=MQ2Krust - Win32 Release
!MESSAGE No configuration specified. Defaulting to MQ2Krust - Win32 Release.
!ENDIF 

!IF "$(CFG)" != "MQ2Krust - Win32 Release"
!MESSAGE Invalid configuration "$(CFG)" specified.
!MESSAGE You can specify a configuration when running NMAKE
!MESSAGE by defining the macro CFG on the command line. For example:
!MESSAGE 
!MESSAGE NMAKE /f "MQ2Krust.mak" CFG="MQ2Krust - Win32 Release"
!MESSAGE 
!MESSAGE Possible choices for configuration are:
!MESSAGE 
!MESSAGE "MQ2Krust - Win32 Release" (based on "Win32 (x86) Dynamic-Link Library")
!MESSAGE 
!ERROR An invalid configuration is specified.
!ENDIF 

!IF "$(OS)" == "Windows_NT"
NULL=
!ELSE 
NULL=nul
!ENDIF 

OUTDIR=.\../Release
INTDIR=.\Intermediate
# Begin Custom Macros
OutDir=.\../Release
# End Custom Macros

!IF "$(RECURSE)" == "0" 

ALL : "$(OUTDIR)\MQ2Krust.dll"

!ELSE 

ALL : "MQ2Main - Win32 Release" "macroquest - Win32 Release" "$(OUTDIR)\MQ2Krust.dll"

!ENDIF 

!IF "$(RECURSE)" == "1" 
CLEAN :"macroquest - Win32 ReleaseCLEAN" "MQ2Main - Win32 ReleaseCLEAN" 
!ELSE 
CLEAN :
!ENDIF 
	-@erase "$(INTDIR)\MQ2Krust.obj"
	-@erase "$(INTDIR)\vc60.idb"
	-@erase "$(OUTDIR)\MQ2Krust.dll"
	-@erase "$(OUTDIR)\MQ2Krust.exp"
	-@erase "$(OUTDIR)\MQ2Krust.lib"

"$(OUTDIR)" :
    if not exist "$(OUTDIR)/$(NULL)" mkdir "$(OUTDIR)"

"$(INTDIR)" :
    if not exist "$(INTDIR)/$(NULL)" mkdir "$(INTDIR)"

CPP=cl.exe
CPP_PROJ=/nologo /Zp1 /MT /W3 /GX /O2 /D "WIN32" /D "NDEBUG" /D "_WINDOWS" /D "_MBCS" /D "_USRDLL" /D "CINTERFACE" /Fp"$(INTDIR)\MQ2Krust.pch" /YX /Fo"$(INTDIR)\\" /Fd"$(INTDIR)\\" /FD /c 

.c{$(INTDIR)}.obj::
   $(CPP) @<<
   $(CPP_PROJ) $< 
<<

.cpp{$(INTDIR)}.obj::
   $(CPP) @<<
   $(CPP_PROJ) $< 
<<

.cxx{$(INTDIR)}.obj::
   $(CPP) @<<
   $(CPP_PROJ) $< 
<<

.c{$(INTDIR)}.sbr::
   $(CPP) @<<
   $(CPP_PROJ) $< 
<<

.cpp{$(INTDIR)}.sbr::
   $(CPP) @<<
   $(CPP_PROJ) $< 
<<

.cxx{$(INTDIR)}.sbr::
   $(CPP) @<<
   $(CPP_PROJ) $< 
<<

MTL=midl.exe
MTL_PROJ=/nologo /D "NDEBUG" /mktyplib203 /win32 
RSC=rc.exe
BSC32=bscmake.exe
BSC32_FLAGS=/nologo /o"$(OUTDIR)\MQ2Krust.bsc" 
BSC32_SBRS= \
	
LINK32=link.exe
LINK32_FLAGS=kernel32.lib user32.lib gdi32.lib winspool.lib comdlg32.lib advapi32.lib shell32.lib ole32.lib oleaut32.lib uuid.lib odbc32.lib odbccp32.lib ..\Detours\lib60\detours.lib ..\Release\MQ2Main.lib /nologo /dll /incremental:no /pdb:"$(OUTDIR)\MQ2Krust.pdb" /machine:I386 /out:"$(OUTDIR)\MQ2Krust.dll" /implib:"$(OUTDIR)\MQ2Krust.lib" /OPT:ICF 
LINK32_OBJS= \
	"$(INTDIR)\MQ2Krust.obj" \
	"$(OUTDIR)\MQ2Main.lib"

"$(OUTDIR)\MQ2Krust.dll" : "$(OUTDIR)" $(DEF_FILE) $(LINK32_OBJS)
    $(LINK32) @<<
  $(LINK32_FLAGS) $(LINK32_OBJS)
<<


!IF "$(NO_EXTERNAL_DEPS)" != "1"
!IF EXISTS("MQ2Krust.dep")
!INCLUDE "MQ2Krust.dep"
!ELSE 
!MESSAGE Warning: cannot find "MQ2Krust.dep"
!ENDIF 
!ENDIF 


!IF "$(CFG)" == "MQ2Krust - Win32 Release"
SOURCE=.\MQ2Krust.cpp

"$(INTDIR)\MQ2Krust.obj" : $(SOURCE) "$(INTDIR)"


!IF  "$(CFG)" == "MQ2Krust - Win32 Release"

"macroquest - Win32 Release" : 
   cd "\mq2\macroquest2\MacroQuest"
   $(MAKE) /$(MAKEFLAGS) /F .\MacroQuest.mak CFG="macroquest - Win32 Release" 
   cd "..\MQ2Krust"

"macroquest - Win32 ReleaseCLEAN" : 
   cd "\mq2\macroquest2\MacroQuest"
   $(MAKE) /$(MAKEFLAGS) /F .\MacroQuest.mak CFG="macroquest - Win32 Release" RECURSE=1 CLEAN 
   cd "..\MQ2Krust"

!ENDIF 

!IF  "$(CFG)" == "MQ2Krust - Win32 Release"

"MQ2Main - Win32 Release" : 
   cd "\mq2\macroquest2\MQ2Main"
   $(MAKE) /$(MAKEFLAGS) /F .\MQ2Main.mak CFG="MQ2Main - Win32 Release" 
   cd "..\MQ2Krust"

"MQ2Main - Win32 ReleaseCLEAN" : 
   cd "\mq2\macroquest2\MQ2Main"
   $(MAKE) /$(MAKEFLAGS) /F .\MQ2Main.mak CFG="MQ2Main - Win32 Release" RECURSE=1 CLEAN 
   cd "..\MQ2Krust"

!ENDIF 


!ENDIF 

