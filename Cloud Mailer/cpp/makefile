#
# Cloud Mail 2022 C++ Edition - Demo Application
#
# Copyright (c) 2023 /n software inc. - All rights reserved. - www.nsoftware.com
#

ifeq ($(shell uname -s), "Darwin") # MACOS
R_PATH=-Wl,-rpath,../../lib64/,-rpath,.

FRAMEWORK = -framework Carbon -framework Security
LD_FLAGS = -lcloudmail.22.0 -L../../lib64/
# EM: iconv is needed for multiple CBT products
LD_FLAGS_SRC = -lz -ldl 
OS_CFLAGS = -D UNIX64 -arch x86_64
MACOS = "darwin% Darwin% macos%"

all:
	g++ $(OS_CFLAGS) -D UNIX -o cloudmailer cloudmailer.cpp  -I../../include/ $(LD_FLAGS) $(FRAMEWORK) $(R_PATH) 
else # LINUX
ifeq ($(shell uname -m), x86_64)
  LIB=lib64
else
  LIB=lib
endif

R_PATH=-Wl,-rpath,../../$(LIB)/,-rpath,.
LD_FLAGS = -lcloudmail  -L../../$(LIB)/
LD_FLAGS_SRC = -lz -ldl 
# EM: enabling code generator options for hardware-accelerated encryption

all:
	g++ -D UNIX -o cloudmailer cloudmailer.cpp  -I../../include/ $(LD_FLAGS) $(R_PATH) 

src: ../../src/cloudmail.o
	g++ -D UNIX -o cloudmailer ../../src/cloudmail.o cloudmailer.cpp  -I../../include/ $(LD_FLAGS_SRC)

../../src/cloudmail.o:
	g++ -c -fno-exceptions -fno-rtti -fPIC -o ../../src/cloudmail.o ../../src/cloudmail.cpp

clean:
	rm -f ../../src/*.o
	rm -f cloudmailer *.o
endif