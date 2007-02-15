!include "../global.mak"

ALL : "$(OUTDIR)\MQ2Vanguard.dll"

CLEAN :
	-@erase "$(INTDIR)\MQ2Vanguard.obj"
	-@erase "$(INTDIR)\vc60.idb"
	-@erase "$(OUTDIR)\MQ2Vanguard.dll"
	-@erase "$(OUTDIR)\MQ2Vanguard.exp"
	-@erase "$(OUTDIR)\MQ2Vanguard.lib"
	-@erase "$(OUTDIR)\MQ2Vanguard.pdb"


LINK32=link.exe
LINK32_FLAGS=kernel32.lib user32.lib gdi32.lib winspool.lib comdlg32.lib advapi32.lib shell32.lib ole32.lib oleaut32.lib uuid.lib odbc32.lib odbccp32.lib $(DETLIB) ..\Release\MQ2Main.lib /nologo /dll /incremental:no /pdb:"$(OUTDIR)\MQ2Vanguard.pdb" /debug /machine:I386 /out:"$(OUTDIR)\MQ2Vanguard.dll" /implib:"$(OUTDIR)\MQ2Vanguard.lib" /OPT:NOICF /OPT:NOREF 
LINK32_OBJS= \
	"$(INTDIR)\MQ2Vanguard.obj" \
	"$(OUTDIR)\MQ2Main.lib"

"$(OUTDIR)\MQ2Vanguard.dll" : "$(OUTDIR)" $(DEF_FILE) $(LINK32_OBJS)
    $(LINK32) $(LINK32_FLAGS) $(LINK32_OBJS)


!IF "$(NO_EXTERNAL_DEPS)" != "1"
!IF EXISTS("MQ2Vanguard.dep")
!INCLUDE "MQ2Vanguard.dep"
!ELSE 
!MESSAGE Warning: cannot find "MQ2Vanguard.dep"
!ENDIF 
!ENDIF 


SOURCE=.\MQ2Vanguard.cpp

"$(INTDIR)\MQ2Vanguard.obj" : $(SOURCE) "$(INTDIR)"

