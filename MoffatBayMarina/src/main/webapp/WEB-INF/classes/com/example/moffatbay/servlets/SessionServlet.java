package com.example.moffatbay.servlets;

import java.io.IOException;
import java.io.PrintWriter;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

public class SessionServlet extends HttpServlet {

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        response.setContentType("application/json");
        PrintWriter out = response.getWriter();
        HttpSession session = request.getSession();

        try {
            // 1. Read JSON data from request
            StringBuilder sb = new StringBuilder();
            String s;
            while ((s = request.getReader().readLine()) != null) {
                sb.append(s);
            }
            JsonObject jsonInput = JsonParser.parseString(sb.toString()).getAsJsonObject();

            String boatLength = jsonInput.get("boatLength").getAsString();
            String checkInDate = jsonInput.get("checkInDate").getAsString();
            String email = jsonInput.get("email").getAsString();  //Get User

            // 2. Store data in session
            session.setAttribute("boatLength", boatLength);
            session.setAttribute("checkInDate", checkInDate);
            session.setAttribute("email", email);  //Store User

            // 3. Send success response
            JsonObject jsonOutput = new JsonObject();
            jsonOutput.addProperty("message", "Session data stored successfully!");
            out.print(jsonOutput.toString());
            out.flush();

        } catch (Exception e) {
            response.setStatus(400); // Bad Request
            JsonObject jsonOutput = new JsonObject();
            jsonOutput.addProperty("message", "Invalid input data!");
            out.print(jsonOutput.toString());
            out.flush();
        }
    }
}