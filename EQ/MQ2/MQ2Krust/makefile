!include "../global.mak"

ALL : "$(OUTDIR)\MQ2Krust.dll"

CLEAN :
	-@erase "$(INTDIR)\MQ2Krust.obj"
	-@erase "$(INTDIR)\vc60.idb"
	-@erase "$(OUTDIR)\MQ2Krust.dll"
	-@erase "$(OUTDIR)\MQ2Krust.exp"
	-@erase "$(OUTDIR)\MQ2Krust.lib"
	-@erase "$(OUTDIR)\MQ2Krust.pdb"


LINK32=link.exe
LINK32_FLAGS=kernel32.lib user32.lib gdi32.lib winspool.lib comdlg32.lib advapi32.lib shell32.lib ole32.lib oleaut32.lib uuid.lib odbc32.lib odbccp32.lib $(DETLIB) ..\Release\MQ2Main.lib /nologo /dll /incremental:no /pdb:"$(OUTDIR)\MQ2Krust.pdb" /debug /machine:I386 /out:"$(OUTDIR)\MQ2Krust.dll" /implib:"$(OUTDIR)\MQ2Krust.lib" /OPT:NOICF /OPT:NOREF 
LINK32_OBJS= \
	"$(INTDIR)\MQ2Krust.obj" \
	"$(OUTDIR)\MQ2Main.lib"

"$(OUTDIR)\MQ2Krust.dll" : "$(OUTDIR)" $(DEF_FILE) $(LINK32_OBJS)
    $(LINK32) $(LINK32_FLAGS) $(LINK32_OBJS)


!IF "$(NO_EXTERNAL_DEPS)" != "1"
!IF EXISTS("MQ2Krust.dep")
!INCLUDE "MQ2Krust.dep"
!ELSE 
!MESSAGE Warning: cannot find "MQ2Krust.dep"
!ENDIF 
!ENDIF 


SOURCE=.\MQ2Krust.cpp

"$(INTDIR)\MQ2Krust.obj" : $(SOURCE) "$(INTDIR)"

